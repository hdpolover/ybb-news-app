<?php

namespace App\Filament\User\Resources\User\PostResource\Pages;

use App\Filament\User\Resources\User\PostResource;
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
        return $data;
    }
}
