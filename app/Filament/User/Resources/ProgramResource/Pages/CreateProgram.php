<?php

namespace App\Filament\User\Resources\ProgramResource\Pages;

use App\Filament\User\Resources\ProgramResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreateProgram extends CreateRecord
{
    protected static string $resource = ProgramResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kind'] = 'program';
        $data['tenant_id'] = session('current_tenant_id');
        $data['created_by'] = Filament::auth()->id();
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Create the program record with nested data
        $programData = $this->data['program'] ?? [];
        
        if (!empty($programData)) {
            $this->record->program()->create($programData);
        }
    }
}
