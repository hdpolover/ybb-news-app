<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Post;
use App\Models\Media;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SystemHealthWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Get total users across all tenants
        $totalUsers = User::count();
        
        // Get new users this month
        $newUsersThisMonth = User::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Get total published posts
        $totalPublishedPosts = Post::where('status', 'published')->count();
        
        // Get posts published this month
        $postsPublishedThisMonth = Post::where('status', 'published')
            ->whereYear('published_at', now()->year)
            ->whereMonth('published_at', now()->month)
            ->count();

        // Get storage usage (approximate from media table)
        $totalStorageMB = Media::sum(DB::raw('COALESCE(file_size, 0)')) / 1024 / 1024;
        $totalStorageGB = $totalStorageMB / 1024;
        
        // Get media count
        $totalMedia = Media::count();

        // Get content breakdown
        $contentByKind = Post::select('kind', DB::raw('count(*) as count'))
            ->where('status', 'published')
            ->groupBy('kind')
            ->pluck('count', 'kind')
            ->toArray();

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description(sprintf(
                    '%d new users this month',
                    $newUsersThisMonth
                ))
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart([
                    $totalUsers - 60,
                    $totalUsers - 50,
                    $totalUsers - 40,
                    $totalUsers - 30,
                    $totalUsers - 20,
                    $totalUsers - 10,
                    $totalUsers
                ]),

            Stat::make('Published Content', number_format($totalPublishedPosts))
                ->description(sprintf(
                    '%d published this month',
                    $postsPublishedThisMonth
                ))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->chart([
                    $totalPublishedPosts - 100,
                    $totalPublishedPosts - 80,
                    $totalPublishedPosts - 60,
                    $totalPublishedPosts - 40,
                    $totalPublishedPosts - 20,
                    $totalPublishedPosts - 10,
                    $totalPublishedPosts
                ]),

            Stat::make('Media Library', number_format($totalMedia) . ' files')
                ->description(sprintf(
                    '%.2f GB storage used',
                    $totalStorageGB
                ))
                ->descriptionIcon('heroicon-m-photo')
                ->color('warning')
                ->chart([
                    $totalMedia - 50,
                    $totalMedia - 40,
                    $totalMedia - 30,
                    $totalMedia - 20,
                    $totalMedia - 10,
                    $totalMedia - 5,
                    $totalMedia
                ]),

            Stat::make('Content Mix', 
                sprintf(
                    '%d posts, %d programs, %d jobs',
                    ($contentByKind['page'] ?? 0) + ($contentByKind['news'] ?? 0) + ($contentByKind['guide'] ?? 0),
                    $contentByKind['program'] ?? 0,
                    $contentByKind['job'] ?? 0
                )
            )
                ->description('Published content breakdown')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('primary')
                ->chart([
                    ($contentByKind['page'] ?? 0) + ($contentByKind['news'] ?? 0) + ($contentByKind['guide'] ?? 0),
                    $contentByKind['program'] ?? 0,
                    $contentByKind['job'] ?? 0,
                    0,
                    0,
                    0,
                    $totalPublishedPosts
                ]),
        ];
    }
}
