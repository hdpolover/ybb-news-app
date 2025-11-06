<?php

namespace App\Filament\User\Resources\PostResource\Pages;

use App\Filament\User\Resources\PostResource;
use App\Models\PostRevision;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Facades\Filament;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Filament::auth()->user();
        $data['updated_by'] = $user->id;
        return $data;
    }

    protected function afterSave(): void
    {
        // Create a revision after saving
        $record = $this->getRecord();
        $user = Filament::auth()->user();
        
        // Get the last revision number
        $lastRevision = PostRevision::where('post_id', $record->id)
            ->orderBy('revision_number', 'desc')
            ->first();
        
        $revisionNumber = $lastRevision ? $lastRevision->revision_number + 1 : 1;
        
        // Create new revision
        PostRevision::create([
            'post_id' => $record->id,
            'user_id' => $user->id,
            'title' => $record->title,
            'slug' => $record->slug,
            'content' => $record->content,
            'excerpt' => $record->excerpt,
            'revision_number' => $revisionNumber,
            'change_summary' => 'Post updated',
            'meta' => [
                'status' => $record->status,
                'kind' => $record->kind,
                'published_at' => $record->published_at?->toDateTimeString(),
            ],
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
