<?php

namespace App\Filament\User\Resources\PostResource\Pages;

use App\Filament\User\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Post created successfully';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        \Illuminate\Support\Facades\Log::info('CreatePost: mutateFormDataBeforeCreate called', ['data' => $data]);
        
        $user = Filament::auth()->user();
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;
        $data['tenant_id'] = session('current_tenant_id');
        
        // Set published_at to now if status is published and published_at is empty
        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        
        // Clear published_at if not published
        if (($data['status'] ?? 'draft') !== 'published') {
            $data['published_at'] = null;
        }
        
        // Clear scheduled_at if not scheduled
        if (($data['status'] ?? 'draft') !== 'scheduled') {
            $data['scheduled_at'] = null;
        }
        
        return $data;
    }
    
    protected function onValidationError(\Illuminate\Validation\ValidationException $exception): void
    {
        \Filament\Notifications\Notification::make()
            ->title('Validation Error')
            ->body('Please check the form for errors.')
            ->danger()
            ->send();
            
        parent::onValidationError($exception);
    }
}
