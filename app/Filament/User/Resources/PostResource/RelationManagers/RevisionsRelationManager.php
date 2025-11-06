<?php

namespace App\Filament\User\Resources\PostResource\RelationManagers;

use App\Models\PostRevision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';

    protected static ?string $title = 'Revision History';

    protected static ?string $icon = 'heroicon-o-clock';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('revision_number')
                    ->label('Revision #')
                    ->disabled(),
                
                Forms\Components\TextInput::make('user.name')
                    ->label('Author')
                    ->disabled(),
                
                Forms\Components\Textarea::make('change_summary')
                    ->label('Changes')
                    ->disabled()
                    ->rows(3),
                
                Forms\Components\Section::make('Content Snapshot')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('excerpt')
                            ->disabled()
                            ->rows(3),
                        
                        Forms\Components\RichEditor::make('content')
                            ->disabled(),
                    ])
                    ->collapsed(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('revision_number')
                    ->label('Rev #')
                    ->sortable()
                    ->weight('semibold'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('change_summary')
                    ->label('Changes')
                    ->limit(50)
                    ->wrap()
                    ->placeholder('No summary provided'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('revision_number', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn (PostRevision $record) => "Revision #{$record->revision_number}")
                    ->modalWidth('7xl'),
                
                Tables\Actions\Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (PostRevision $record) {
                        $post = $this->getOwnerRecord();
                        
                        // Create a new revision from current state before restoring
                        $currentRevisionNumber = $post->revisions()->max('revision_number') ?? 0;
                        
                        PostRevision::create([
                            'post_id' => $post->id,
                            'user_id' => auth()->id(),
                            'title' => $post->title,
                            'slug' => $post->slug,
                            'content' => $post->content,
                            'excerpt' => $post->excerpt,
                            'meta' => [
                                'cover_image_url' => $post->cover_image_url,
                                'meta_title' => $post->meta_title,
                                'meta_description' => $post->meta_description,
                            ],
                            'revision_number' => $currentRevisionNumber + 1,
                            'change_summary' => 'Auto-saved before restoring revision #' . $record->revision_number,
                        ]);
                        
                        // Restore from selected revision
                        $post->update([
                            'title' => $record->title,
                            'slug' => $record->slug,
                            'content' => $record->content,
                            'excerpt' => $record->excerpt,
                        ]);
                        
                        if ($record->meta) {
                            $post->update([
                                'cover_image_url' => $record->meta['cover_image_url'] ?? null,
                                'meta_title' => $record->meta['meta_title'] ?? null,
                                'meta_description' => $record->meta['meta_description'] ?? null,
                            ]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Revision restored')
                            ->body("Content restored to revision #{$record->revision_number}")
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Restore Revision')
                    ->modalDescription('Current content will be saved as a new revision before restoring this version.')
                    ->modalSubmitActionLabel('Restore'),
            ])
            ->headerActions([])
            ->emptyStateHeading('No revisions yet')
            ->emptyStateDescription('Revisions will be automatically created when content is updated.')
            ->emptyStateIcon('heroicon-o-clock');
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
