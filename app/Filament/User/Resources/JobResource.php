<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\JobResource\Pages;
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

class JobResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Opportunities';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Jobs';

    protected static ?string $modelLabel = 'Job';

    protected static ?string $pluralModelLabel = 'Jobs';

    public static function getEloquentQuery(): Builder
    {
        $tenantId = session('current_tenant_id');
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        $query = parent::getEloquentQuery()
            ->where('kind', 'job')
            ->with('job'); // Eager load job details
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        // Authors can only see their own jobs
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
                    ->default('job'),
                    
                Tabs::make('JobTabs')->tabs([
                    Tabs\Tab::make('Content')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Job Title')
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
                                ->label('Full Job Description')
                                ->columnSpanFull()
                                ->required(),
                        ]),
                    
                    Tabs\Tab::make('Job Details')
                        ->schema([
                            Forms\Components\TextInput::make('job.company_name')
                                ->label('Company Name')
                                ->required(),
                            Forms\Components\TextInput::make('job.title_override')
                                ->label('Title Override')
                                ->helperText('Optional: Override the job title for display purposes'),
                            Forms\Components\Select::make('job.employment_type')
                                ->label('Employment Type')
                                ->options([
                                    'full_time' => 'Full Time',
                                    'part_time' => 'Part Time',
                                    'contract' => 'Contract',
                                    'temporary' => 'Temporary',
                                    'internship' => 'Internship',
                                    'volunteer' => 'Volunteer',
                                ])
                                ->required(),
                            Forms\Components\Select::make('job.workplace_type')
                                ->label('Workplace Type')
                                ->options([
                                    'onsite' => 'On-site',
                                    'remote' => 'Remote',
                                    'hybrid' => 'Hybrid',
                                ])
                                ->required(),
                            Forms\Components\Select::make('job.experience_level')
                                ->label('Experience Level')
                                ->options([
                                    'entry' => 'Entry Level',
                                    'mid' => 'Mid Level',
                                    'senior' => 'Senior Level',
                                    'lead' => 'Lead',
                                    'manager' => 'Manager',
                                    'director' => 'Director',
                                    'executive' => 'Executive',
                                ]),
                            Forms\Components\TextInput::make('job.location_city')
                                ->label('City')
                                ->required(),
                            Forms\Components\TextInput::make('job.country_code')
                                ->label('Country Code')
                                ->maxLength(2),
                        ]),
                    
                    Tabs\Tab::make('Compensation')
                        ->schema([
                            Forms\Components\TextInput::make('job.min_salary')
                                ->label('Minimum Salary')
                                ->numeric()
                                ->prefix('$'),
                            Forms\Components\TextInput::make('job.max_salary')
                                ->label('Maximum Salary')
                                ->numeric()
                                ->prefix('$'),
                            Forms\Components\Select::make('job.salary_currency')
                                ->label('Currency')
                                ->options([
                                    'USD' => 'USD',
                                    'EUR' => 'EUR',
                                    'GBP' => 'GBP',
                                    'CAD' => 'CAD',
                                    'AUD' => 'AUD',
                                ])
                                ->default('USD'),
                            Forms\Components\Select::make('job.salary_period')
                                ->label('Salary Period')
                                ->options([
                                    'hour' => 'Per Hour',
                                    'day' => 'Per Day',
                                    'week' => 'Per Week',
                                    'month' => 'Per Month',
                                    'year' => 'Per Year',
                                ])
                                ->default('year'),
                        ]),
                    
                    Tabs\Tab::make('Requirements & Responsibilities')
                        ->schema([
                            Forms\Components\RichEditor::make('job.responsibilities')
                                ->label('Responsibilities')
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('job.requirements')
                                ->label('Requirements')
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('job.benefits')
                                ->label('Benefits')
                                ->helperText('Enter benefits separated by commas or new lines')
                                ->rows(4)
                                ->columnSpanFull(),
                        ]),
                    
                    Tabs\Tab::make('Application')
                        ->schema([
                            Forms\Components\DateTimePicker::make('job.deadline_at')
                                ->label('Application Deadline'),
                            Forms\Components\TextInput::make('job.apply_url')
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
                Tables\Columns\TextColumn::make('job.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('job.employment_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'full_time' => 'success',
                        'part_time' => 'info',
                        'contract' => 'warning',
                        'internship' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'temporary' => 'Temporary',
                        'internship' => 'Internship',
                        'volunteer' => 'Volunteer',
                        default => $state ?? 'N/A',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('job.workplace_type')
                    ->label('Workplace')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'remote' => 'success',
                        'hybrid' => 'warning',
                        'onsite' => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'onsite' => 'On-site',
                        'remote' => 'Remote',
                        'hybrid' => 'Hybrid',
                        default => $state ?? 'N/A',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('job.location_city')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('job.deadline_at')
                    ->label('Deadline')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->color(fn ($record): string => 
                        $record->job && $record->job->deadline_at && $record->job->deadline_at->isPast() 
                            ? 'danger' 
                            : 'success'
                    )
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('job.employment_type')
                    ->label('Employment Type')
                    ->relationship('job', 'employment_type')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'temporary' => 'Temporary',
                        'internship' => 'Internship',
                        'volunteer' => 'Volunteer',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('job.workplace_type')
                    ->label('Workplace Type')
                    ->relationship('job', 'workplace_type')
                    ->options([
                        'onsite' => 'On-site',
                        'remote' => 'Remote',
                        'hybrid' => 'Hybrid',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('job.experience_level')
                    ->label('Experience Level')
                    ->relationship('job', 'experience_level')
                    ->options([
                        'entry' => 'Entry Level',
                        'mid' => 'Mid Level',
                        'senior' => 'Senior Level',
                        'lead' => 'Lead',
                        'manager' => 'Manager',
                        'director' => 'Director',
                        'executive' => 'Executive',
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
                Tables\Filters\Filter::make('salary_range')
                    ->label('Salary Range')
                    ->form([
                        Forms\Components\TextInput::make('min_salary')
                            ->label('Minimum')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('max_salary')
                            ->label('Maximum')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_salary'],
                                fn (Builder $query, $amount): Builder => 
                                    $query->whereHas('job', fn ($q) => $q->where('min_salary', '>=', $amount)),
                            )
                            ->when(
                                $data['max_salary'],
                                fn (Builder $query, $amount): Builder => 
                                    $query->whereHas('job', fn ($q) => $q->where('max_salary', '<=', $amount)),
                            );
                    }),
                Tables\Filters\Filter::make('active_deadlines')
                    ->label('Active (Not Expired)')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereHas('job', fn ($q) => $q->where('deadline_at', '>=', now()))
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }
}
