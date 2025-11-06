<?php

namespace App\Filament\User\Widgets;

use App\Models\AdImpression;
use App\Models\AdClick;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AdImpressionsTrendWidget extends ChartWidget
{
    protected static ?string $heading = 'Impressions & Clicks Trend';

    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $tenantId = session('current_tenant_id');
        
        // Get data for last 30 days
        $days = 30;
        $dates = collect();
        $impressions = [];
        $clicks = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates->push($date);
            
            $impressions[] = AdImpression::where('tenant_id', $tenantId)
                ->whereDate('created_at', $date)
                ->count();
                
            $clicks[] = AdClick::where('tenant_id', $tenantId)
                ->whereDate('created_at', $date)
                ->count();
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Impressions',
                    'data' => $impressions,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Clicks',
                    'data' => $clicks,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $dates->map(fn($date) => date('M j', strtotime($date)))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
