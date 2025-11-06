<?php

namespace App\Filament\User\Pages;

use App\Models\Post;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ContentCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.user.pages.content-calendar';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Content Calendar';
    
    public array $events = [];
    
    public function mount(): void
    {
        $this->events = $this->loadEvents();
    }
    
    protected function loadEvents(): array
    {
        $tenantId = session('current_tenant_id');
        
        // Return empty array if no tenant is selected
        if (!$tenantId) {
            return [];
        }
        
        try {
            $posts = Post::where('tenant_id', $tenantId)
                ->whereIn('status', ['scheduled', 'published', 'draft'])
                ->where(function ($query) {
                    $query->whereNotNull('published_at')
                        ->orWhereNotNull('scheduled_at');
                })
                ->with('author')
                ->get();
            
            return $posts->map(function ($post) {
                $date = $post->published_at ?? $post->scheduled_at ?? $post->created_at;
                
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'start' => $date->toIso8601String(),
                    'backgroundColor' => match($post->status) {
                        'published' => '#10b981',
                        'scheduled' => '#3b82f6',
                        'draft' => '#6b7280',
                        default => '#6b7280',
                    },
                    'borderColor' => match($post->status) {
                        'published' => '#059669',
                        'scheduled' => '#2563eb',
                        'draft' => '#4b5563',
                        default => '#4b5563',
                    },
                    'url' => route('filament.app.resources.posts.edit', ['record' => $post->id]),
                    'extendedProps' => [
                        'status' => $post->status,
                        'author' => $post->author->name ?? 'Unknown',
                    ],
                ];
            })->toArray();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ContentCalendar getEvents error: ' . $e->getMessage());
            return [];
        }
    }
    
    public function updateEventDate(int $postId, string $newDate): void
    {
        $tenantId = session('current_tenant_id');
        
        $post = Post::where('tenant_id', $tenantId)
            ->where('id', $postId)
            ->firstOrFail();
        
        // Check authorization
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->can('update', $post)) {
            Notification::make()
                ->title('Unauthorized')
                ->body('You are not authorized to update this post.')
                ->danger()
                ->send();
            return;
        }
        
        // Update the appropriate date field based on status
        if ($post->status === 'scheduled') {
            $post->scheduled_at = $newDate;
        } elseif ($post->status === 'published') {
            $post->published_at = $newDate;
        }
        
        $post->save();
        
        // Refresh events
        $this->events = $this->loadEvents();
        
        Notification::make()
            ->title('Success')
            ->body('Post date updated successfully.')
            ->success()
            ->send();
    }
}
