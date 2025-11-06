<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ProgramResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class ProgramResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Opportunities';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Programs';

    protected static ?string $modelLabel = 'Program';

    protected static ?string $pluralModelLabel = 'Programs';

    public static function getEloquentQuery(): Builder
    {
        $tenantId = session('current_tenant_id');
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        $query = parent::getEloquentQuery()
            ->where('kind', 'program')
            ->with('program'); // Eager load program details
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        // Authors can only see their own programs
        if ($user && $user->hasRole(['Author', 'Contributor'])) {
            $query->where('created_by', $user->id);
        }
        
        return $query;
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        return $user !== null;
    }

    public static function canEdit($record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        
        if (!$user) {
            return false;
        }
        
        if ($user->hasRole(['Tenant Admin', 'Editor'])) {
            return true;
        }
        
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
        
        if ($user->hasRole(['Tenant Admin', 'Editor'])) {
            return true;
        }
        
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
            ->schema([
                Forms\Components\Hidden::make('kind')
                    ->default('program'),
                    
                Tabs::make('ProgramTabs')->tabs([
                    Tabs\Tab::make('Content')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Program Title')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(Post::class, 'slug', ignoreRecord: true)
                                ->columnSpanFull(),
                            Forms\Components\FileUpload::make('cover_image_url')
                                ->label('Cover Image')
                                ->image()
                                ->disk('public')
                                ->directory('post-covers')
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('excerpt')
                                ->label('Short Description')
                                ->rows(3)
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('content')
                                ->label('Full Description')
                                ->columnSpanFull()
                                ->required(),
                        ]),
                    
                    Tabs\Tab::make('Program Details')
                        ->schema([
                            Forms\Components\Select::make('program.program_type')
                                ->label('Program Type')
                                ->options([
                                    'scholarship' => 'Scholarship',
                                    'opportunity' => 'Opportunity',
                                    'internship' => 'Internship',
                                    'fellowship' => 'Fellowship',
                                    'grant' => 'Grant',
                                    'competition' => 'Competition',
                                    'conference' => 'Conference',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('program.organizer_name')
                                ->label('Organizer')
                                ->required(),
                            Forms\Components\TextInput::make('program.location_text')
                                ->label('Location')
                                ->required(),
                            Forms\Components\TextInput::make('program.country_code')
                                ->label('Country Code')
                                ->maxLength(2),
                            Forms\Components\Select::make('program.funding_type')
                                ->label('Funding Type')
                                ->options([
                                    'fully_funded' => 'Fully Funded',
                                    'partially_funded' => 'Partially Funded',
                                    'not_funded' => 'Not Funded',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('program.stipend_amount')
                                ->label('Stipend Amount')
                                ->numeric()
                                ->prefix('$'),
                            Forms\Components\TextInput::make('program.fee_amount')
                                ->label('Fee Amount')
                                ->numeric()
                                ->prefix('$'),
                            Forms\Components\TextInput::make('program.program_length_text')
                                ->label('Program Duration')
                                ->placeholder('e.g., 6 months, 1 year'),
                            Forms\Components\DateTimePicker::make('program.deadline_at')
                                ->label('Application Deadline')
                                ->required(),
                            Forms\Components\Checkbox::make('program.is_rolling')
                                ->label('Rolling Deadline'),
                            Forms\Components\Textarea::make('program.eligibility_text')
                                ->label('Eligibility Requirements')
                                ->rows(4),
                            Forms\Components\TextInput::make('program.apply_url')
                                ->label('Application URL')
                                ->url()
                                ->required(),
                        ]),
                    
                    Tabs\Tab::make('SEO')
                        ->schema([
                            Forms\Components\TextInput::make('meta_title')
                                ->label('Meta Title'),
                            Forms\Components\Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(3),
                            Forms\Components\FileUpload::make('og_image_url')
                                ->label('Social Media Image (OG)')
                                ->image()
                                ->disk('public')
                                ->directory('post-og-images'),
                            Forms\Components\TextInput::make('canonical_url')
                                ->label('Canonical URL')
                                ->url(),
                        ]),
                    
                    Tabs\Tab::make('Categories & Tags')
                        ->schema([
                            Forms\Components\Select::make('terms')
                                ->relationship('terms', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->columnSpanFull(),
                        ]),
                    
                    Tabs\Tab::make('Publishing')
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'review' => 'Pending Review',
                                    'scheduled' => 'Scheduled',
                                    'published' => 'Published',
                                    'archived' => 'Archived',
                                ])
                                ->default('draft')
                                ->required()
                                ->live(),
                            Forms\Components\DateTimePicker::make('scheduled_at')
                                ->label('Schedule For')
                                ->visible(fn(Get $get): bool => $get('status') === 'scheduled'),
                            Forms\Components\DateTimePicker::make('published_at')
                                ->label('Publish Date')
                                ->visible(fn(Get $get): bool => $get('status') === 'published'),
                        ]),
                ])->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('program.program_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scholarship' => 'success',
                        'internship' => 'info',
                        'fellowship' => 'warning',
                        'opportunity' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.organizer_name')
                    ->label('Organizer')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('program.funding_type')
                    ->label('Funding')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fully_funded' => 'success',
                        'partially_funded' => 'warning',
                        'not_funded' => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fully_funded' => 'Fully Funded',
                        'partially_funded' => 'Partially Funded',
                        'not_funded' => 'Not Funded',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('program.deadline_at')
                    ->label('Deadline')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->color(fn ($record): string => 
                        $record->program && $record->program->deadline_at && $record->program->deadline_at->isPast() 
                            ? 'danger' 
                            : 'success'
                    ),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'review' => 'info',
                        'scheduled' => 'warning',
                        'archived' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program.program_type')
                    ->label('Program Type')
                    ->relationship('program', 'program_type')
                    ->options([
                        'scholarship' => 'Scholarship',
                        'opportunity' => 'Opportunity',
                        'internship' => 'Internship',
                        'fellowship' => 'Fellowship',
                        'grant' => 'Grant',
                        'competition' => 'Competition',
                        'conference' => 'Conference',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('program.funding_type')
                    ->label('Funding Type')
                    ->relationship('program', 'funding_type')
                    ->options([
                        'fully_funded' => 'Fully Funded',
                        'partially_funded' => 'Partially Funded',
                        'not_funded' => 'Not Funded',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'review' => 'Pending Review',
                        'scheduled' => 'Scheduled',
                        'archived' => 'Archived',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('deadline')
                    ->label('Deadline')
                    ->form([
                        Forms\Components\DatePicker::make('deadline_from')
                            ->label('Deadline from'),
                        Forms\Components\DatePicker::make('deadline_until')
                            ->label('Deadline until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['deadline_from'],
                                fn (Builder $query, $date): Builder => 
                                    $query->whereHas('program', fn ($q) => $q->whereDate('deadline_at', '>=', $date)),
                            )
                            ->when(
                                $data['deadline_until'],
                                fn (Builder $query, $date): Builder => 
                                    $query->whereHas('program', fn ($q) => $q->whereDate('deadline_at', '<=', $date)),
                            );
                    }),
                Tables\Filters\Filter::make('active_deadlines')
                    ->label('Active (Not Expired)')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereHas('program', fn ($q) => $q->where('deadline_at', '>=', now()))
                    )
                    ->toggle(),
            ])
            ->actions([
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
            ->defaultSort('program.deadline_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}
