<?php

namespace App\Filament\User\Resources\MediaResource\Pages;

use App\Filament\User\Resources\MediaResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Filament::auth()->user();
        $data['tenant_id'] = session('current_tenant_id');
        $data['uploaded_by'] = $user->id;
        $data['disk'] = 'public';
        return $data;
    }
}
