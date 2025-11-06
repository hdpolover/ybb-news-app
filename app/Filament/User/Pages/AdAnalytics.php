<?php

namespace App\Filament\User\Pages;

use App\Models\Ad;
use App\Models\AdImpression;
use App\Models\AdClick;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\DB;

class AdAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.user.pages.ad-analytics';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Ad Analytics';

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\User\Widgets\AdPerformanceWidget::class,
            \App\Filament\User\Widgets\AdImpressionsTrendWidget::class,
            \App\Filament\User\Widgets\TopPerformingAdsWidget::class,
        ];
    }
}
