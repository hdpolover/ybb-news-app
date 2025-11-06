<?php

namespace App\Filament\User\Pages;

use App\Models\Post;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SeoAudit extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    protected static string $view = 'filament.user.pages.seo-audit';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'SEO Audit';

    public function getSeoIssues(): array
    {
        $tenantId = session('current_tenant_id');
        $issues = [];
        
        // Return empty array if no tenant is selected
        if (!$tenantId) {
            return [];
        }
        
        $publishedCount = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->count();
        
        // Check for missing meta descriptions
        $postsWithoutMeta = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('meta_description')
                    ->orWhere('meta_description', '');
            })
            ->count();
        
        if ($postsWithoutMeta > 0) {
            $percentage = round(($postsWithoutMeta / max($publishedCount, 1)) * 100);
            $issues[] = [
                'type' => 'warning',
                'title' => 'Missing Meta Descriptions',
                'description' => "{$postsWithoutMeta} of {$publishedCount} published posts ({$percentage}%) don't have meta descriptions.",
                'action' => 'Add 120-160 character meta descriptions to improve search engine visibility and click-through rates.',
                'severity' => 'high',
            ];
        }
        
        // Check for short meta descriptions
        $shortMeta = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereNotNull('meta_description')
            ->whereRaw('LENGTH(meta_description) < 120')
            ->count();
        
        if ($shortMeta > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Short Meta Descriptions',
                'description' => "{$shortMeta} posts have meta descriptions shorter than 120 characters.",
                'action' => 'Expand meta descriptions to 120-160 characters for better search visibility.',
                'severity' => 'medium',
            ];
        }
        
        // Check for long meta descriptions
        $longMeta = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereNotNull('meta_description')
            ->whereRaw('LENGTH(meta_description) > 160')
            ->count();
        
        if ($longMeta > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Long Meta Descriptions',
                'description' => "{$longMeta} posts have meta descriptions longer than 160 characters.",
                'action' => 'Shorten meta descriptions to 120-160 characters to avoid truncation in search results.',
                'severity' => 'low',
            ];
        }
        
        // Check for missing featured images
        $postsWithoutImages = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereNull('cover_image_url')
            ->count();
        
        if ($postsWithoutImages > 0) {
            $percentage = round(($postsWithoutImages / max($publishedCount, 1)) * 100);
            $issues[] = [
                'type' => 'warning',
                'title' => 'Missing Featured Images',
                'description' => "{$postsWithoutImages} posts ({$percentage}%) don't have featured images.",
                'action' => 'Add high-quality featured images (1200x630px recommended) to improve social sharing and SEO.',
                'severity' => 'medium',
            ];
        }
        
        // Check for missing OG images
        $postsWithoutOG = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereNull('og_image_url')
            ->count();
        
        if ($postsWithoutOG > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Missing Open Graph Images',
                'description' => "{$postsWithoutOG} posts don't have specific OG images for social media.",
                'action' => 'Add OG images (1200x630px) to control how posts appear when shared on social media.',
                'severity' => 'low',
            ];
        }
        
        // Check for very short content (likely thin content)
        $thinContent = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereRaw('LENGTH(content) < 300')
            ->count();
        
        if ($thinContent > 0) {
            $issues[] = [
                'type' => 'warning',
                'title' => 'Thin Content',
                'description' => "{$thinContent} posts have less than 300 characters (very short).",
                'action' => 'Expand content to at least 300 words (1500+ characters) for better SEO performance.',
                'severity' => 'high',
            ];
        }
        
        // Check for short content (300-1500 chars)
        $shortContent = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereRaw('LENGTH(content) >= 300 AND LENGTH(content) < 1500')
            ->count();
        
        if ($shortContent > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Short Content',
                'description' => "{$shortContent} posts have less than 300 words.",
                'action' => 'Consider expanding content to 500+ words for comprehensive coverage and better rankings.',
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
                'action' => 'Make titles unique to avoid confusion, improve user experience, and prevent SEO cannibalization.',
                'severity' => 'high',
            ];
        }
        
        // Check for duplicate slugs (should not happen, but good to check)
        $duplicateSlugs = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->select('slug', DB::raw('COUNT(*) as count'))
            ->groupBy('slug')
            ->having('count', '>', 1)
            ->count();
        
        if ($duplicateSlugs > 0) {
            $issues[] = [
                'type' => 'danger',
                'title' => 'Duplicate URL Slugs',
                'description' => "{$duplicateSlugs} URL slugs are duplicated (critical issue).",
                'action' => 'Fix immediately! Duplicate URLs cause conflicts and SEO issues.',
                'severity' => 'high',
            ];
        }
        
        // Check for long titles
        $longTitles = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereRaw('LENGTH(title) > 60')
            ->count();
        
        if ($longTitles > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Long Titles',
                'description' => "{$longTitles} posts have titles longer than 60 characters.",
                'action' => 'Keep titles between 30-60 characters to avoid truncation in search results.',
                'severity' => 'low',
            ];
        }
        
        // Check for short titles
        $shortTitles = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->whereRaw('LENGTH(title) < 30')
            ->count();
        
        if ($shortTitles > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Short Titles',
                'description' => "{$shortTitles} posts have titles shorter than 30 characters.",
                'action' => 'Expand titles to 30-60 characters for better keyword coverage and click-through rates.',
                'severity' => 'low',
            ];
        }
        
        // Check for missing excerpts
        $missingExcerpts = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('excerpt')
                    ->orWhere('excerpt', '');
            })
            ->count();
        
        if ($missingExcerpts > 0) {
            $issues[] = [
                'type' => 'info',
                'title' => 'Missing Excerpts',
                'description' => "{$missingExcerpts} posts don't have custom excerpts.",
                'action' => 'Add compelling excerpts (150-160 characters) to improve post listings and social shares.',
                'severity' => 'low',
            ];
        }
        
        // Check for posts without terms (categories/tags)
        $postsWithoutTerms = Post::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->doesntHave('terms')
            ->count();
        
        if ($postsWithoutTerms > 0) {
            $issues[] = [
                'type' => 'warning',
                'title' => 'Uncategorized Content',
                'description' => "{$postsWithoutTerms} posts don't have any categories or tags.",
                'action' => 'Organize content with relevant categories and tags for better navigation and SEO.',
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
        $tenantId = session('current_tenant_id');
        
        // Return empty array if no tenant is selected
        if (!$tenantId) {
            return [];
        }
        
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
        if (empty($post->cover_image_url)) {
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
        
        if (empty($post->cover_image_url)) {
            $issues[] = 'Missing featured image';
        }
        
        if (strlen($post->slug) > 75) {
            $issues[] = 'URL slug too long';
        }
        
        return $issues;
    }
    
    public function getPageSpeedData(): ?array
    {
        $tenantId = session('current_tenant_id');
        
        if (!$tenantId) {
            return null;
        }
        
        // Get the tenant's primary domain
        $tenant = \App\Models\Tenant::find($tenantId);
        if (!$tenant || !$tenant->domain) {
            return null;
        }
        
        $url = 'https://' . $tenant->domain;
        
        // Cache for 1 hour to avoid hitting API limits
        return Cache::remember("pagespeed_{$tenantId}", 3600, function () use ($url) {
            try {
                $response = Http::timeout(30)->get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed', [
                    'url' => $url,
                    'strategy' => 'mobile', // or 'desktop'
                    'category' => ['performance', 'accessibility', 'best-practices', 'seo'],
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    return [
                        'performance' => round(($data['lighthouseResult']['categories']['performance']['score'] ?? 0) * 100),
                        'accessibility' => round(($data['lighthouseResult']['categories']['accessibility']['score'] ?? 0) * 100),
                        'best_practices' => round(($data['lighthouseResult']['categories']['best-practices']['score'] ?? 0) * 100),
                        'seo' => round(($data['lighthouseResult']['categories']['seo']['score'] ?? 0) * 100),
                        'core_web_vitals' => [
                            'lcp' => $data['lighthouseResult']['audits']['largest-contentful-paint']['displayValue'] ?? 'N/A',
                            'fid' => $data['lighthouseResult']['audits']['max-potential-fid']['displayValue'] ?? 'N/A',
                            'cls' => $data['lighthouseResult']['audits']['cumulative-layout-shift']['displayValue'] ?? 'N/A',
                        ],
                        'opportunities' => $this->extractOpportunities($data['lighthouseResult']['audits'] ?? []),
                    ];
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('PageSpeed API Error: ' . $e->getMessage());
            }
            
            return null;
        });
    }
    
    protected function extractOpportunities(array $audits): array
    {
        $opportunities = [];
        
        // Common optimization opportunities
        $checks = [
            'unused-css-rules' => 'Remove unused CSS',
            'unused-javascript' => 'Remove unused JavaScript',
            'modern-image-formats' => 'Use modern image formats (WebP, AVIF)',
            'offscreen-images' => 'Defer offscreen images',
            'uses-text-compression' => 'Enable text compression',
            'uses-responsive-images' => 'Properly size images',
            'efficient-animated-content' => 'Use video formats for animated content',
        ];
        
        foreach ($checks as $key => $description) {
            if (isset($audits[$key]) && isset($audits[$key]['score']) && $audits[$key]['score'] < 0.9) {
                $opportunities[] = [
                    'title' => $description,
                    'savings' => $audits[$key]['displayValue'] ?? 'Potential savings available',
                ];
            }
        }
        
        return array_slice($opportunities, 0, 5); // Top 5 opportunities
    }
}
