<?php

namespace App\Filament\User\Resources\TermResource\Pages;

use App\Filament\User\Resources\TermResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTerm extends CreateRecord
{
    protected static string $resource = TermResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = session('current_tenant_id');
        $data['post_count'] = 0;
        return $data;
    }
}
