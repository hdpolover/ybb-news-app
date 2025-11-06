<?php

namespace App\Filament\User\Resources\PostResource\Pages;

use App\Filament\User\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Filament::auth()->user();
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;
        $data['tenant_id'] = session('current_tenant_id');
        
        // Set published_at to now if status is published and published_at is empty
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        
        // Clear published_at if not published
        if ($data['status'] !== 'published') {
            $data['published_at'] = null;
        }
        
        // Clear scheduled_at if not scheduled
        if ($data['status'] !== 'scheduled') {
            $data['scheduled_at'] = null;
        }
        
        return $data;
    }
}
