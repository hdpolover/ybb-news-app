<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected static ?int $sort = 2;
    
    protected static string $view = 'filament.user.widgets.quick-actions-widget';

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];
}
