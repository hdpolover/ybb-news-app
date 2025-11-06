<?php

namespace App\Filament\User\Resources\JobResource\Pages;

use App\Filament\User\Resources\JobResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreateJob extends CreateRecord
{
    protected static string $resource = JobResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['kind'] = 'job';
        $data['tenant_id'] = session('current_tenant_id');
        $data['created_by'] = Filament::auth()->id();
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Create the job record with nested data
        $jobData = $this->data['job'] ?? [];
        
        if (!empty($jobData)) {
            $this->record->job()->create($jobData);
        }
    }
}
