<?php

namespace App\Filament\User\Resources\User\PostResource\Pages;

use App\Filament\User\Resources\User\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Facades\Filament;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeUpdate(array $data): array
    {
        $user = Filament::auth()->user();
        $data['updated_by'] = $user->id;
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
