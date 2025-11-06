<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\TeamRoleResource\Pages;
use Filament\Resources\Resource;

class TeamRoleResource extends Resource
{
    protected static ?string $model = null; // No model - this is a view-only resource

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Roles & Permissions';

    protected static ?string $slug = 'roles-permissions';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamRoles::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

