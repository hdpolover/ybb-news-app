<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

class Analytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.user.pages.analytics';

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?string $title = 'Analytics';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 6;

    public function getHeading(): string
    {
        return 'Analytics Dashboard';
    }

    public function getSubheading(): ?string
    {
        return 'Track your content performance, traffic sources, and audience engagement.';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\User\Widgets\ContentPerformanceWidget::class,
            \App\Filament\User\Widgets\TrafficSourcesWidget::class,
            \App\Filament\User\Widgets\TopContentWidget::class,
        ];
    }
}
