<?php

namespace App\Filament\User\Widgets;

use App\Models\Post;
use App\Models\AnalyticsEvent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ContentPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenantId = session('current_tenant_id');
        
        // Return empty stats if no tenant is selected
        if (!$tenantId) {
            return [
                Stat::make('Total Views (30d)', '0')
                    ->description('Please select a tenant')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
                Stat::make('Avg. Engagement', '0:00')
                    ->description('No tenant selected')
                    ->color('gray'),
                Stat::make('Published This Month', '0')
                    ->description('No tenant selected')
                    ->color('gray'),
                Stat::make('Unique Visitors', '0')
                    ->description('No tenant selected')
                    ->color('gray'),
            ];
        }
        
        $startDate = now()->subDays(30);

        // Total views in last 30 days
        $totalViews = AnalyticsEvent::where('tenant_id', $tenantId)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->count();

        // Previous period for comparison
        $previousViews = AnalyticsEvent::where('tenant_id', $tenantId)
            ->where('event_type', 'page_view')
            ->whereBetween('created_at', [now()->subDays(60), $startDate])
            ->count();

        $viewsChange = $previousViews > 0 
            ? round((($totalViews - $previousViews) / $previousViews) * 100, 1)
            : 0;

        // Average engagement time
        $avgEngagement = AnalyticsEvent::where('tenant_id', $tenantId)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('custom_data->duration')
            ->avg(DB::raw("CAST(JSON_EXTRACT(custom_data, '$.duration') AS UNSIGNED)"));

        $avgEngagementFormatted = $avgEngagement 
            ? gmdate('i:s', (int) $avgEngagement)
            : '0:00';

        // Published content this month
        $publishedThisMonth = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereMonth('published_at', now()->month)
            ->whereYear('published_at', now()->year)
            ->count();

        $publishedLastMonth = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereMonth('published_at', now()->subMonth()->month)
            ->whereYear('published_at', now()->subMonth()->year)
            ->count();

        $publishedChange = $publishedLastMonth > 0
            ? round((($publishedThisMonth - $publishedLastMonth) / $publishedLastMonth) * 100, 1)
            : 0;

        // Unique visitors
        $uniqueVisitors = AnalyticsEvent::where('tenant_id', $tenantId)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->distinct('session_id')
            ->count('session_id');

        return [
            Stat::make('Total Views (30d)', number_format($totalViews))
                ->description($viewsChange >= 0 ? "+{$viewsChange}% from previous period" : "{$viewsChange}% from previous period")
                ->descriptionIcon($viewsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($viewsChange >= 0 ? 'success' : 'danger')
                ->chart($this->getViewsChart($tenantId)),

            Stat::make('Avg. Engagement', $avgEngagementFormatted)
                ->description('Time spent per page')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('Published This Month', $publishedThisMonth)
                ->description($publishedChange >= 0 ? "+{$publishedChange}% from last month" : "{$publishedChange}% from last month")
                ->descriptionIcon($publishedChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($publishedChange >= 0 ? 'success' : 'warning')
                ->chart($this->getPublishedChart($tenantId)),

            Stat::make('Unique Visitors', number_format($uniqueVisitors))
                ->description('Last 30 days')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }

    protected function getViewsChart(?string $tenantId): array
    {
        if (!$tenantId) {
            return [0, 0, 0, 0, 0, 0, 0];
        }
        
        $data = AnalyticsEvent::where('tenant_id', $tenantId)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        return array_pad($data, 7, 0);
    }

    protected function getPublishedChart(?string $tenantId): array
    {
        if (!$tenantId) {
            return [0, 0, 0, 0, 0, 0, 0];
        }
        
        $data = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->where('published_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(published_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        return array_pad($data, 7, 0);
    }
}
