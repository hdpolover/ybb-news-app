<?php

namespace App\Filament\Resources\SeoLandingResource\Pages;

use App\Filament\Resources\SeoLandingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeoLanding extends EditRecord
{
    protected static string $resource = SeoLandingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
