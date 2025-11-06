<?php

namespace App\Filament\User\Widgets;

use App\Models\Post;
use App\Models\AnalyticsEvent;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TopContentWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $tenantId = session('current_tenant_id');
        
        if (!$tenantId) {
            return $table
                ->query(Post::query()->whereRaw('1 = 0'))
                ->columns([
                    Tables\Columns\TextColumn::make('title')
                        ->default('No tenant selected')
                        ->label('Message'),
                ])
                ->heading('Top Content (Last 30 Days)')
                ->paginated(false);
        }
        
        $startDate = now()->subDays(30);

        // Get top posts by view count
        $topPostIds = AnalyticsEvent::where('tenant_id', $tenantId)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('custom_data->post_id')
            ->select(
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(custom_data, '$.post_id')) as post_id"),
                DB::raw('COUNT(*) as views')
            )
            ->groupBy('post_id')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get()
            ->pluck('views', 'post_id');

        // If no posts have views, return empty table
        if ($topPostIds->isEmpty()) {
            return $table
                ->query(Post::query()->whereRaw('1 = 0'))
                ->columns([
                    Tables\Columns\TextColumn::make('title')
                        ->default('No data available')
                        ->label('Message'),
                ])
                ->heading('Top Content (Last 30 Days)')
                ->paginated(false);
        }

        return $table
            ->query(
                Post::query()
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'published')
                    ->whereIn('id', $topPostIds->keys())
                    ->with(['author'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->weight('semibold')
                    ->url(fn (Post $record) => route('filament.app.resources.posts.edit', $record), shouldOpenInNewTab: true),

                Tables\Columns\BadgeColumn::make('kind')
                    ->label('Type')
                    ->colors([
                        'primary' => 'page',
                        'success' => 'news',
                        'warning' => 'guide',
                        'info' => 'program',
                        'danger' => 'job',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('views')
                    ->label('Views (30d)')
                    ->badge()
                    ->color('success')
                    ->state(fn (Post $record) => number_format($topPostIds[$record->id] ?? 0)),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->date()
                    ->sortable(),
            ])
            ->heading('Top Content (Last 30 Days)')
            ->defaultSort(fn (Builder $query) => 
                $query->orderByRaw("FIELD(id, " . $topPostIds->keys()->implode(',') . ")")
            )
            ->paginated(false);
    }
}
