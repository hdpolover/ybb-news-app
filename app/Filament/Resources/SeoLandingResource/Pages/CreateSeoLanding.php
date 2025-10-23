<?php

namespace App\Filament\Resources\SeoLandingResource\Pages;

use App\Filament\Resources\SeoLandingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSeoLanding extends CreateRecord
{
    protected static string $resource = SeoLandingResource::class;

    protected function getFormActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Save'),

            Actions\CreateAction::make('createAnother')
                ->label('Save & create another'),
        ];
    }
}
