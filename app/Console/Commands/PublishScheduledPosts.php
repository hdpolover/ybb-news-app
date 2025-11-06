<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Publish posts that are scheduled for now or earlier';

    public function handle(): int
    {
        $now = now();
        
        // Find all scheduled posts that should be published
        $posts = Post::where('status', 'scheduled')
            ->where('scheduled_at', '<=', $now)
            ->get();
        
        $count = 0;
        
        foreach ($posts as $post) {
            $post->status = 'published';
            $post->published_at = $post->scheduled_at;
            $post->scheduled_at = null;
            $post->save();
            
            $count++;
            
            Log::info("Published scheduled post: {$post->title} (ID: {$post->id})");
            $this->info("Published: {$post->title}");
        }
        
        if ($count === 0) {
            $this->info('No scheduled posts to publish.');
        } else {
            $this->info("Published {$count} post(s).");
        }
        
        return Command::SUCCESS;
    }
}
