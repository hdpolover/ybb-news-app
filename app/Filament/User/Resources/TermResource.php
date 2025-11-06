<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\TermResource\Pages;
use App\Filament\User\Resources\TermResource\RelationManagers;
use App\Models\Term;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Facades\Filament;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Terms';

    protected static ?string $modelLabel = 'Term';

    protected static ?string $pluralModelLabel = 'Terms';

    public static function getEloquentQuery(): Builder
    {
        $tenantId = session('current_tenant_id');
        $query = parent::getEloquentQuery();
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        return $query;
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        // Only tenant admins and editors can create terms
        return $user && $user->hasRole(['Tenant Admin', 'Editor']);
    }

    public static function canEdit($record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        return $user && $user->hasRole(['Tenant Admin', 'Editor']);
    }

    public static function canDelete($record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        return $user && $user->hasRole(['Tenant Admin', 'Editor']);
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
                Forms\Components\Section::make('Term Details')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'category' => 'Category',
                                'tag' => 'Tag',
                                'location' => 'Location',
                                'skill' => 'Skill',
                                'industry' => 'Industry',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(Term::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('parent_id')
                            ->relationship('parent', 'name', fn(Builder $query) => 
                                $query->where('tenant_id', session('current_tenant_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Only assign a parent if this is a sub-category.'),
                        Forms\Components\ColorPicker::make('color')
                            ->helperText('Used for category badges and tags'),
                        Forms\Components\TextInput::make('icon')
                            ->maxLength(255)
                            ->helperText('Heroicon name (e.g., heroicon-o-academic-cap)'),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_featured')
                            ->helperText('Show this term in featured sections'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'category' => 'success',
                        'tag' => 'info',
                        'location' => 'warning',
                        'skill' => 'primary',
                        'industry' => 'secondary',
                        default => 'gray',
                    }),
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('post_count')
                    ->numeric()
                    ->sortable()
                    ->label('Posts'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'category' => 'Category',
                        'tag' => 'Tag',
                        'location' => 'Location',
                        'skill' => 'Skill',
                        'industry' => 'Industry',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTerms::route('/'),
            'create' => Pages\CreateTerm::route('/create'),
            'edit' => Pages\EditTerm::route('/{record}/edit'),
        ];
    }
}
