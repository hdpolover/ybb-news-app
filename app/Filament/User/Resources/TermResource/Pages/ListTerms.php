<?php

namespace App\Filament\User\Resources\TermResource\Pages;

use App\Filament\User\Resources\TermResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTerms extends ListRecords
{
    protected static string $resource = TermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
