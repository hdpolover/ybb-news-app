<?php

namespace App\Filament\User\Widgets;

use App\Models\Post;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPosts extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $tenantId = session('current_tenant_id');

        return $table
            ->query(
                Post::query()
                    ->where('tenant_id', $tenantId)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image_url')
                    ->label('Cover')
                    ->disk('public')
                    ->size(40),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('kind')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'news' => 'info',
                        'guide' => 'success',
                        'program' => 'warning',
                        'job' => 'primary',
                        'page' => 'gray',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'archived' => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->url(fn (Post $record): string => route('filament.app.resources.posts.edit', $record))
                    ->icon('heroicon-o-pencil'),
            ]);
    }
}
