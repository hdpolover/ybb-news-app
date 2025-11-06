<?php

namespace App\Filament\User\Pages;

use App\Models\Post;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SeoAudit extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    protected static string $view = 'filament.user.pages.seo-audit';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'SEO Audit';

    public function getSeoIssues(): array
    {
        $tenantId = session('tenant_id');
        $issues = [];
        
        // Check for missing meta descriptions
        $postsWithoutMeta = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('meta_description')
                    ->orWhere('meta_description', '');
            })
            ->count();
        
        if ($postsWithoutMeta > 0) {
            $issues[] = [
                'type' => 'warning',
                'title' => 'Missing Meta Descriptions',
                'description' => "{$postsWithoutMeta} published posts don't have meta descriptions.",
                'action' => 'Add meta descriptions to improve search engine visibility.',
                'severity' => 'medium',
            ];
        }
        
        // Check for missing featured images
        $postsWithoutImages = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereNull('featured_image')
            ->count();
        
        if ($postsWithoutImages > 0) {
            $issues[] = [
                'type' => 'warning',
                'title' => 'Missing Featured Images',
                'description' => "{$postsWithoutImages} published posts don't have featured images.",
                'action' => 'Add featured images to improve social sharing and SEO.',
                'severity' => 'low',
            ];
        }
        
        // Check for short content
        $shortPosts = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereRaw('LENGTH(content) < 300')
            ->count();
        
        if ($shortPosts > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Short Content',
                'description' => "{$shortPosts} published posts have less than 300 characters.",
                'action' => 'Consider expanding content for better SEO performance.',
                'severity' => 'low',
            ];
        }
        
        // Check for duplicate titles
        $duplicateTitles = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->select('title', DB::raw('COUNT(*) as count'))
            ->groupBy('title')
            ->having('count', '>', 1)
            ->count();
        
        if ($duplicateTitles > 0) {
            $issues[] = [
                'type' => 'danger',
                'title' => 'Duplicate Titles',
                'description' => "{$duplicateTitles} titles are used by multiple posts.",
                'action' => 'Make titles unique to avoid confusion and improve SEO.',
                'severity' => 'high',
            ];
        }
        
        // Check for missing alt text on images (simplified check)
        $postsWithImages = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereNotNull('featured_image')
            ->count();
        
        if ($postsWithImages > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Image Accessibility',
                'description' => "Review {$postsWithImages} posts with images for proper alt text.",
                'action' => 'Ensure all images have descriptive alt text for accessibility and SEO.',
                'severity' => 'medium',
            ];
        }
        
        return $issues;
    }
    
    public function getSeoScore(): int
    {
        $issues = $this->getSeoIssues();
        $score = 100;
        
        foreach ($issues as $issue) {
            $score -= match($issue['severity']) {
                'high' => 15,
                'medium' => 10,
                'low' => 5,
                default => 0,
            };
        }
        
        return max(0, $score);
    }
    
    public function getRecentPosts(): array
    {
        $tenantId = session('tenant_id');
        
        return Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'url' => route('filament.app.resources.posts.edit', ['record' => $post->id]),
                    'seo_score' => $this->calculatePostSeoScore($post),
                    'issues' => $this->getPostSeoIssues($post),
                ];
            })
            ->toArray();
    }
    
    protected function calculatePostSeoScore($post): int
    {
        $score = 100;
        
        // Check meta description
        if (empty($post->meta_description)) {
            $score -= 20;
        } elseif (strlen($post->meta_description) < 120 || strlen($post->meta_description) > 160) {
            $score -= 10;
        }
        
        // Check title length
        if (strlen($post->title) < 30 || strlen($post->title) > 60) {
            $score -= 10;
        }
        
        // Check content length
        if (strlen($post->content) < 300) {
            $score -= 20;
        }
        
        // Check featured image
        if (empty($post->featured_image)) {
            $score -= 15;
        }
        
        // Check slug
        if (strlen($post->slug) > 75) {
            $score -= 5;
        }
        
        return max(0, $score);
    }
    
    protected function getPostSeoIssues($post): array
    {
        $issues = [];
        
        if (empty($post->meta_description)) {
            $issues[] = 'Missing meta description';
        } elseif (strlen($post->meta_description) < 120) {
            $issues[] = 'Meta description too short';
        } elseif (strlen($post->meta_description) > 160) {
            $issues[] = 'Meta description too long';
        }
        
        if (strlen($post->title) < 30) {
            $issues[] = 'Title too short';
        } elseif (strlen($post->title) > 60) {
            $issues[] = 'Title too long';
        }
        
        if (strlen($post->content) < 300) {
            $issues[] = 'Content too short';
        }
        
        if (empty($post->featured_image)) {
            $issues[] = 'Missing featured image';
        }
        
        if (strlen($post->slug) > 75) {
            $issues[] = 'URL slug too long';
        }
        
        return $issues;
    }
}
