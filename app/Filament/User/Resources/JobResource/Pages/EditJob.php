<?php

namespace App\Filament\User\Resources\JobResource\Pages;

use App\Filament\User\Resources\JobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Facades\Filament;

class EditJob extends EditRecord
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load job data into the form
        if ($this->record->job) {
            $data['job'] = $this->record->job->toArray();
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Filament::auth()->id();
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Update or create the job record
        $jobData = $this->data['job'] ?? [];
        
        if (!empty($jobData)) {
            if ($this->record->job) {
                $this->record->job()->update($jobData);
            } else {
                $this->record->job()->create($jobData);
            }
        }
    }
}
