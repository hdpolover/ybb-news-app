<?php

namespace App\Filament\Resources\SeoLandingResource\Pages;

use App\Filament\Resources\SeoLandingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeoLandings extends ListRecords
{
    protected static string $resource = SeoLandingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
