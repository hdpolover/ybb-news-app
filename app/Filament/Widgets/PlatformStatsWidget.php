<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PlatformStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Get tenant counts by status
        $tenantStats = Tenant::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalTenants = array_sum($tenantStats);
        $activeTenants = $tenantStats['active'] ?? 0;
        $suspendedTenants = $tenantStats['suspended'] ?? 0;
        $pendingTenants = $tenantStats['pending'] ?? 0;

        // Get new tenants this month
        $newTenantsThisMonth = Tenant::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Get last month's new tenants for comparison
        $newTenantsLastMonth = Tenant::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        $tenantGrowth = $newTenantsLastMonth > 0 
            ? (($newTenantsThisMonth - $newTenantsLastMonth) / $newTenantsLastMonth) * 100 
            : 0;

        // Get tenant growth chart data (last 7 months)
        $tenantGrowthChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Tenant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $tenantGrowthChart[] = $count;
        }

        return [
            Stat::make('Total Tenants', $totalTenants)
                ->description(sprintf(
                    '%d active, %d suspended, %d pending',
                    $activeTenants,
                    $suspendedTenants,
                    $pendingTenants
                ))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart($tenantGrowthChart),

            Stat::make('Active Tenants', $activeTenants)
                ->description(sprintf('%.1f%% of total tenants', $totalTenants > 0 ? ($activeTenants / $totalTenants) * 100 : 0))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart(array_fill(0, 7, $activeTenants)),

            Stat::make('New This Month', $newTenantsThisMonth)
                ->description(
                    $tenantGrowth > 0 
                        ? sprintf('%.1f%% increase', $tenantGrowth)
                        : ($tenantGrowth < 0 
                            ? sprintf('%.1f%% decrease', abs($tenantGrowth))
                            : 'Same as last month')
                )
                ->descriptionIcon(
                    $tenantGrowth > 0 
                        ? 'heroicon-m-arrow-trending-up' 
                        : ($tenantGrowth < 0 
                            ? 'heroicon-m-arrow-trending-down' 
                            : 'heroicon-m-minus')
                )
                ->color($tenantGrowth >= 0 ? 'success' : 'warning')
                ->chart($tenantGrowthChart),

            Stat::make('Suspended/Pending', $suspendedTenants + $pendingTenants)
                ->description(sprintf(
                    '%d suspended, %d pending approval',
                    $suspendedTenants,
                    $pendingTenants
                ))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($suspendedTenants + $pendingTenants > 0 ? 'warning' : 'gray')
                ->chart(array_fill(0, 7, $suspendedTenants + $pendingTenants)),
        ];
    }
}
