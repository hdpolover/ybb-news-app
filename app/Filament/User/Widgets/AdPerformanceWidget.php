<?php

namespace App\Filament\User\Widgets;

use App\Models\Ad;
use App\Models\AdImpression;
use App\Models\AdClick;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AdPerformanceWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $tenantId = session('current_tenant_id');
        
        // Get current period stats (last 30 days)
        $currentStart = now()->subDays(30);
        $currentEnd = now();
        
        // Get previous period stats (30-60 days ago)
        $previousStart = now()->subDays(60);
        $previousEnd = now()->subDays(30);
        
        // Total Impressions
        $currentImpressions = AdImpression::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->count();
            
        $previousImpressions = AdImpression::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();
            
        $impressionsTrend = $previousImpressions > 0 
            ? (($currentImpressions - $previousImpressions) / $previousImpressions) * 100 
            : 0;
        
        // Total Clicks
        $currentClicks = AdClick::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->count();
            
        $previousClicks = AdClick::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();
            
        $clicksTrend = $previousClicks > 0 
            ? (($currentClicks - $previousClicks) / $previousClicks) * 100 
            : 0;
        
        // Click-Through Rate
        $currentCTR = $currentImpressions > 0 
            ? ($currentClicks / $currentImpressions) * 100 
            : 0;
            
        $previousCTR = $previousImpressions > 0 
            ? ($previousClicks / $previousImpressions) * 100 
            : 0;
            
        $ctrTrend = $previousCTR > 0 
            ? (($currentCTR - $previousCTR) / $previousCTR) * 100 
            : 0;
        
        // Active Ads
        $activeAds = Ad::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
            
        $totalAds = Ad::where('tenant_id', $tenantId)->count();

        return [
            Stat::make('Total Impressions', number_format($currentImpressions))
                ->description(abs($impressionsTrend) > 0 ? abs(round($impressionsTrend, 1)) . '% ' . ($impressionsTrend > 0 ? 'increase' : 'decrease') : 'No change')
                ->descriptionIcon($impressionsTrend > 0 ? 'heroicon-m-arrow-trending-up' : ($impressionsTrend < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($impressionsTrend > 0 ? 'success' : ($impressionsTrend < 0 ? 'danger' : 'gray'))
                ->chart($this->getImpressionsOverTimeChart()),
                
            Stat::make('Total Clicks', number_format($currentClicks))
                ->description(abs($clicksTrend) > 0 ? abs(round($clicksTrend, 1)) . '% ' . ($clicksTrend > 0 ? 'increase' : 'decrease') : 'No change')
                ->descriptionIcon($clicksTrend > 0 ? 'heroicon-m-arrow-trending-up' : ($clicksTrend < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($clicksTrend > 0 ? 'success' : ($clicksTrend < 0 ? 'danger' : 'gray'))
                ->chart($this->getClicksOverTimeChart()),
                
            Stat::make('Click-Through Rate', round($currentCTR, 2) . '%')
                ->description(abs($ctrTrend) > 0 ? abs(round($ctrTrend, 1)) . '% ' . ($ctrTrend > 0 ? 'increase' : 'decrease') : 'No change')
                ->descriptionIcon($ctrTrend > 0 ? 'heroicon-m-arrow-trending-up' : ($ctrTrend < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($ctrTrend > 0 ? 'success' : ($ctrTrend < 0 ? 'danger' : 'gray')),
                
            Stat::make('Active Ads', $activeAds . ' / ' . $totalAds)
                ->description('Currently running campaigns')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('info'),
        ];
    }
    
    protected function getImpressionsOverTimeChart(): array
    {
        $tenantId = session('current_tenant_id');
        
        $data = AdImpression::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
            
        return array_pad($data, 7, 0);
    }
    
    protected function getClicksOverTimeChart(): array
    {
        $tenantId = session('current_tenant_id');
        
        $data = AdClick::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
            
        return array_pad($data, 7, 0);
    }
}
