<?php

namespace App\Filament\User\Resources\ProgramResource\Pages;

use App\Filament\User\Resources\ProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Facades\Filament;

class EditProgram extends EditRecord
{
    protected static string $resource = ProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load program data into the form
        if ($this->record->program) {
            $data['program'] = $this->record->program->toArray();
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
        // Update or create the program record
        $programData = $this->data['program'] ?? [];
        
        if (!empty($programData)) {
            if ($this->record->program) {
                $this->record->program()->update($programData);
            } else {
                $this->record->program()->create($programData);
            }
        }
    }
}
