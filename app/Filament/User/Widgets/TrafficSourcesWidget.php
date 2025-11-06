<?php

namespace App\Filament\User\Widgets;

use App\Models\AnalyticsEvent;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TrafficSourcesWidget extends ChartWidget
{
    protected static ?string $heading = 'Traffic Sources';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $tenantId = session('tenant_id');
        $startDate = now()->subDays(30);

        $sources = AnalyticsEvent::where('tenant_id', $tenantId)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw("COALESCE(JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.source')), 'Direct') as source"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $labels = $sources->pluck('source')->map(fn($s) => ucfirst($s))->toArray();
        $data = $sources->pluck('count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',   // blue
                        'rgb(16, 185, 129)',   // green
                        'rgb(249, 115, 22)',   // orange
                        'rgb(236, 72, 153)',   // pink
                        'rgb(139, 92, 246)',   // purple
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
