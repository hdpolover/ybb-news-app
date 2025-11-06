<?php

namespace App\Filament\User\Resources\PostResource\RelationManagers;

use App\Models\PostComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'allComments';

    protected static ?string $title = 'Editorial Comments';

    protected static ?string $icon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $recordTitleAttribute = 'comment';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('comment')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                
                Forms\Components\Select::make('type')
                    ->options([
                        'internal' => 'Internal Note',
                        'review' => 'Review Feedback',
                        'approval' => 'Approval Comment',
                    ])
                    ->default('internal')
                    ->required(),
                
                Forms\Components\Toggle::make('is_resolved')
                    ->label('Mark as Resolved')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('comment')
                    ->limit(60)
                    ->wrap()
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'secondary' => 'internal',
                        'warning' => 'review',
                        'success' => 'approval',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'internal' => 'Internal',
                        'review' => 'Review',
                        'approval' => 'Approval',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\IconColumn::make('is_resolved')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Posted')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'internal' => 'Internal',
                        'review' => 'Review',
                        'approval' => 'Approval',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_resolved')
                    ->label('Status')
                    ->placeholder('All comments')
                    ->trueLabel('Resolved')
                    ->falseLabel('Open'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Comment')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (PostComment $record): bool => !$record->is_resolved)
                    ->action(function (PostComment $record) {
                        $record->update(['is_resolved' => true]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Comment resolved')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\Action::make('reopen')
                    ->label('Reopen')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (PostComment $record): bool => $record->is_resolved)
                    ->action(function (PostComment $record) {
                        $record->update(['is_resolved' => false]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Comment reopened')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('resolve')
                        ->label('Mark as Resolved')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['is_resolved' => true]))
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No comments yet')
            ->emptyStateDescription('Add editorial comments, feedback, or notes about this content.')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }
}
