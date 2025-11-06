<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PostResource\Pages;
use App\Filament\User\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Posts';

    protected static ?string $modelLabel = 'Post';

    protected static ?string $pluralModelLabel = 'Posts';

    public static function getEloquentQuery(): Builder
    {
        $tenantId = session('current_tenant_id');
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        $query = parent::getEloquentQuery()
            ->whereIn('kind', ['page', 'news', 'guide']); // Exclude programs and jobs
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        // Authors can only see their own posts
        if ($user && $user->hasRole(['Author', 'Contributor'])) {
            $query->where('created_by', $user->id);
        }
        
        return $query;
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        // All authenticated users can create posts
        return $user !== null;
    }

    public static function canEdit($record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Tenant admins and editors can edit any post
        if ($user->hasRole(['Tenant Admin', 'Editor'])) {
            return true;
        }
        
        // Authors and contributors can only edit their own posts
        if ($user->hasRole(['Author', 'Contributor'])) {
            return $record->created_by === $user->id;
        }
        
        return false;
    }

    public static function canDelete($record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Only tenant admins and editors can delete posts
        if ($user->hasRole(['Tenant Admin', 'Editor'])) {
            return true;
        }
        
        // Authors can only delete their own draft posts
        if ($user->hasRole(['Author'])) {
            return $record->created_by === $user->id && $record->status === 'draft';
        }
        
        return false;
    }

    public static function canDeleteAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        return $user && $user->hasRole(['Tenant Admin', 'Editor']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->model(Post::class)
            ->schema([
                Tabs::make('PostTabs')->tabs([
                    Tabs\Tab::make('Content')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(
                                    table: Post::class,
                                    column: 'slug',
                                    ignoreRecord: true,
                                    modifyRuleUsing: function ($rule, $context, $record) {
                                        $rule = $rule->where('tenant_id', session('current_tenant_id'));
                                        
                                        // Explicitly ignore current record when editing
                                        if ($context === 'edit' && $record) {
                                            $rule = $rule->ignore($record->id);
                                        }
                                        
                                        return $rule;
                                    }
                                )
                                ->columnSpanFull(),
                            Forms\Components\FileUpload::make('cover_image_url')
                                ->label('Cover Image')
                                ->image()
                                ->disk('public')
                                ->directory('post-covers')
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('excerpt')
                                ->rows(3)
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('content')
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('SEO')
                        ->schema([
                            Forms\Components\TextInput::make('meta_title'),
                            Forms\Components\Textarea::make('meta_description'),
                            Forms\Components\FileUpload::make('og_image_url')
                                ->label('Social Media Image (OG)')
                                ->image()
                                ->disk('public')
                                ->directory('post-og-images'),
                            Forms\Components\TextInput::make('canonical_url')
                                ->label('Canonical URL')
                                ->rules(['nullable', 'url'])
                                ->validationAttribute('canonical URL'),
                        ]),
                    Tabs\Tab::make('Details & Relations')
                        ->schema([
                            Forms\Components\Select::make('kind')
                                ->options([
                                    'page' => 'Page',
                                    'news' => 'News',
                                    'guide' => 'Guide',
                                ])
                                ->live()
                                ->default('news')
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'review' => 'Review',
                                    'scheduled' => 'Scheduled',
                                    'published' => 'Published',
                                    'archived' => 'Archived',
                                ])
                                ->default('draft')
                                ->live()
                                ->required()
                                ->helperText('Draft: Save for later | Review: Submit for approval | Scheduled: Publish at specific time | Published: Visible now'),
                            Forms\Components\DateTimePicker::make('published_at')
                                ->label('Publish Date')
                                ->visible(fn(Get $get): bool => $get('status') === 'published')
                                ->dehydrated(fn(Get $get): bool => $get('status') === 'published')
                                ->helperText('Leave empty to use current date/time'),
                            Forms\Components\DateTimePicker::make('scheduled_at')
                                ->label('Schedule For')
                                ->visible(fn(Get $get): bool => $get('status') === 'scheduled')
                                ->dehydrated(fn(Get $get): bool => $get('status') === 'scheduled')
                                ->rules(['required_if:status,scheduled'])
                                ->helperText('Post will be published automatically at this date/time'),
                            Forms\Components\Select::make('terms')
                                ->relationship('terms', 'name', fn($query) => 
                                    $query->where('tenant_id', session('current_tenant_id'))
                                )
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->helperText('Categories, tags, and other taxonomies'),
                        ])->columns(2),
                ])->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Content')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('slug')
                            ->badge()
                            ->color('gray'),
                        Infolists\Components\ImageEntry::make('cover_image_url')
                            ->label('Cover Image')
                            ->disk('public')
                            ->height(200),
                        Infolists\Components\TextEntry::make('excerpt')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('content')
                            ->html()
                            ->columnSpanFull(),
                    ])->columns(2),
                Infolists\Components\Section::make('Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('kind')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'news' => 'info',
                                'guide' => 'success',
                                'page' => 'gray',
                                default => 'secondary',
                            }),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'published' => 'success',
                                'draft' => 'gray',
                                'scheduled' => 'warning',
                                'review' => 'info',
                                'archived' => 'danger',
                                default => 'secondary',
                            }),
                        Infolists\Components\TextEntry::make('author.name')
                            ->label('Author'),
                        Infolists\Components\TextEntry::make('published_at')
                            ->dateTime()
                            ->placeholder('Not published'),
                        Infolists\Components\TextEntry::make('scheduled_at')
                            ->dateTime()
                            ->placeholder('Not scheduled'),
                        Infolists\Components\TextEntry::make('terms.name')
                            ->badge()
                            ->separator(',')
                            ->placeholder('No terms'),
                    ])->columns(3),
                Infolists\Components\Section::make('SEO')
                    ->schema([
                        Infolists\Components\TextEntry::make('meta_title')
                            ->placeholder('No meta title'),
                        Infolists\Components\TextEntry::make('meta_description')
                            ->placeholder('No meta description')
                            ->columnSpanFull(),
                        Infolists\Components\ImageEntry::make('og_image_url')
                            ->label('Social Media Image')
                            ->disk('public')
                            ->height(100)
                            ->placeholder('No OG image'),
                        Infolists\Components\TextEntry::make('canonical_url')
                            ->placeholder('No canonical URL')
                            ->url(fn ($state) => $state)
                            ->openUrlInNewTab(),
                    ])->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image_url')
                    ->label('Cover')
                    ->disk('public')
                    ->size(50),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('kind')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'news' => 'info',
                        'guide' => 'success',
                        'program' => 'warning',
                        'job' => 'primary',
                        'page' => 'gray',
                        default => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'archived' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kind')
                    ->options([
                        'page' => 'Page',
                        'news' => 'News',
                        'guide' => 'Guide',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'archived' => 'Archived',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->label('Published from'),
                        Forms\Components\DatePicker::make('published_until')
                            ->label('Published until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('submitForReview')
                    ->label('Submit for Review')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (Post $record): bool => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(function (Post $record) {
                        $record->update(['status' => 'review']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Submitted for review')
                            ->body('Your content has been submitted and editors will review it.')
                            ->success()
                            ->send();
                        
                        // TODO: Notify editors
                    })
                    ->modalHeading('Submit for Review')
                    ->modalDescription('Your content will be sent to editors for review.')
                    ->modalSubmitActionLabel('Submit'),
                
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ]))),
                    Tables\Actions\BulkAction::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->requiresConfirmation()
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['status' => 'archived']))),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RevisionsRelationManager::class,
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
