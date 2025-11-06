<?php

namespace App\Filament\User\Resources\TeamMemberResource\Pages;

use App\Filament\User\Resources\TeamMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamMembers extends ListRecords
{
    protected static string $resource = TeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Invite Team Member')
                ->icon('heroicon-o-envelope'),
        ];
    }

    public function getTitle(): string
    {
        return 'Team Members';
    }

    public function getHeading(): string
    {
        return 'Team Members';
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
