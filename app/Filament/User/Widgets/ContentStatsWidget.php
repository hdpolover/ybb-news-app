<?php

namespace App\Filament\User\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ContentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenantId = session('current_tenant_id');
        
        if (!$tenantId) {
            return [];
        }

        // Get post counts by status
        $postStats = Post::where('tenant_id', $tenantId)
            ->whereIn('kind', ['page', 'news', 'guide'])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get program counts
        $programTotal = Post::where('tenant_id', $tenantId)
            ->where('kind', 'program')
            ->count();
        
        $programActive = Post::where('tenant_id', $tenantId)
            ->where('kind', 'program')
            ->whereHas('program', function ($query) {
                $query->where('deadline_at', '>=', now());
            })
            ->count();

        // Get job counts
        $jobTotal = Post::where('tenant_id', $tenantId)
            ->where('kind', 'job')
            ->count();
        
        $jobActive = Post::where('tenant_id', $tenantId)
            ->where('kind', 'job')
            ->whereHas('job', function ($query) {
                $query->where('deadline_at', '>=', now())
                    ->orWhereNull('deadline_at');
            })
            ->count();

        // Get content published this month
        $publishedThisMonth = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereYear('published_at', now()->year)
            ->whereMonth('published_at', now()->month)
            ->count();

        // Get last month's published count for comparison
        $publishedLastMonth = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereYear('published_at', now()->subMonth()->year)
            ->whereMonth('published_at', now()->subMonth()->month)
            ->count();

        $publishedChange = $publishedLastMonth > 0 
            ? (($publishedThisMonth - $publishedLastMonth) / $publishedLastMonth) * 100 
            : 0;

        return [
            Stat::make('Total Posts', $postStats['published'] ?? 0)
                ->description(sprintf(
                    '%d draft, %d in review, %d scheduled',
                    $postStats['draft'] ?? 0,
                    $postStats['review'] ?? 0,
                    $postStats['scheduled'] ?? 0
                ))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->chart([7, 12, 9, 14, 18, 15, $postStats['published'] ?? 0]),

            Stat::make('Programs', $programTotal)
                ->description(sprintf('%d active (not expired)', $programActive))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning')
                ->chart([5, 8, 12, 10, 15, 18, $programTotal]),

            Stat::make('Jobs', $jobTotal)
                ->description(sprintf('%d active openings', $jobActive))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info')
                ->chart([3, 7, 5, 9, 12, 15, $jobTotal]),

            Stat::make('Published This Month', $publishedThisMonth)
                ->description(
                    $publishedChange > 0 
                        ? sprintf('%.1f%% increase', $publishedChange)
                        : ($publishedChange < 0 
                            ? sprintf('%.1f%% decrease', abs($publishedChange))
                            : 'No change from last month')
                )
                ->descriptionIcon(
                    $publishedChange > 0 
                        ? 'heroicon-m-arrow-trending-up' 
                        : ($publishedChange < 0 
                            ? 'heroicon-m-arrow-trending-down' 
                            : 'heroicon-m-minus')
                )
                ->color($publishedChange >= 0 ? 'success' : 'danger')
                ->chart(array_fill(0, 7, $publishedThisMonth)),
        ];
    }
}
