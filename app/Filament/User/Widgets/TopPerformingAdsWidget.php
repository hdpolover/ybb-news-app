<?php

namespace App\Filament\User\Widgets;

use App\Models\Ad;
use App\Models\AdImpression;
use App\Models\AdClick;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopPerformingAdsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $tenantId = session('tenant_id');
        
        return $table
            ->heading('Top Performing Ads (Last 30 Days)')
            ->query(
                Ad::query()
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'active')
                    ->select('ads.*')
                    ->selectSub(
                        AdImpression::selectRaw('COUNT(*)')
                            ->whereColumn('ad_id', 'ads.id')
                            ->where('created_at', '>=', now()->subDays(30)),
                        'impressions_count'
                    )
                    ->selectSub(
                        AdClick::selectRaw('COUNT(*)')
                            ->whereColumn('ad_id', 'ads.id')
                            ->where('created_at', '>=', now()->subDays(30)),
                        'clicks_count'
                    )
                    ->orderByDesc('clicks_count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Ad Title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                    
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'primary' => 'banner',
                        'success' => 'sidebar',
                        'warning' => 'inline',
                    ]),
                    
                Tables\Columns\TextColumn::make('position')
                    ->label('Position')
                    ->badge()
                    ->colors([
                        'info' => fn ($state) => in_array($state, ['header', 'footer', 'top']),
                        'gray' => fn ($state) => in_array($state, ['sidebar', 'middle', 'bottom']),
                    ]),
                    
                Tables\Columns\TextColumn::make('impressions_count')
                    ->label('Impressions')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),
                    
                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Clicks')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),
                    
                Tables\Columns\TextColumn::make('ctr')
                    ->label('CTR')
                    ->state(function (Ad $record): string {
                        $impressions = $record->impressions_count ?? 0;
                        $clicks = $record->clicks_count ?? 0;
                        
                        if ($impressions === 0) {
                            return '0%';
                        }
                        
                        $ctr = ($clicks / $impressions) * 100;
                        return round($ctr, 2) . '%';
                    })
                    ->sortable()
                    ->alignEnd()
                    ->color(function ($state): string {
                        $ctr = floatval(str_replace('%', '', $state));
                        if ($ctr >= 2) return 'success';
                        if ($ctr >= 1) return 'warning';
                        return 'gray';
                    }),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('clicks_count', 'desc');
    }
}
