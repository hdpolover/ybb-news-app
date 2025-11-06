<?php

namespace App\Filament\User\Widgets;

use App\Models\Post;
use App\Models\Term;
use App\Models\Media;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $tenantId = session('current_tenant_id');

        if (!$tenantId) {
            return [];
        }

        $totalPosts = Post::where('tenant_id', $tenantId)->count();
        $publishedPosts = Post::where('tenant_id', $tenantId)->where('status', 'published')->count();
        $draftPosts = Post::where('tenant_id', $tenantId)->where('status', 'draft')->count();
        $totalTerms = Term::where('tenant_id', $tenantId)->count();
        $totalMedia = Media::where('tenant_id', $tenantId)->count();
        $totalMediaSize = Media::where('tenant_id', $tenantId)->sum('size');

        return [
            Stat::make('Total Posts', $totalPosts)
                ->description($publishedPosts . ' published, ' . $draftPosts . ' drafts')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success')
                ->chart([7, 12, 8, 14, 10, 15, $totalPosts]),
            Stat::make('Categories & Tags', $totalTerms)
                ->description('Terms available')
                ->descriptionIcon('heroicon-o-tag')
                ->color('warning'),
            Stat::make('Media Files', $totalMedia)
                ->description(number_format($totalMediaSize / 1024 / 1024, 2) . ' MB total')
                ->descriptionIcon('heroicon-o-photo')
                ->color('info'),
        ];
    }
}
