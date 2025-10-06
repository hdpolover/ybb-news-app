<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoLandingResource\Pages;
use App\Models\SeoLanding;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Tabs;

class SeoLandingResource extends Resource
{
    protected static ?string $model = SeoLanding::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'SEO Landing Pages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('SeoLandingTabs')->tabs([
                    Tabs\Tab::make('Main Content')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('content')
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('SEO Settings')
                        ->schema([
                            Forms\Components\TextInput::make('meta_title')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('target_keyword')
                                ->label('Target Keyword'),
                            Forms\Components\Textarea::make('meta_description')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('canonical_url')
                                ->label('Canonical URL')
                                ->url(),
                            Forms\Components\KeyValue::make('schema_markup')
                                ->label('Schema Markup (JSON-LD)'),
                        ]),
                    Tabs\Tab::make('Page Settings')
                        ->schema([
                            Forms\Components\Select::make('tenant_id')
                                ->relationship('tenant', 'name')
                                ->required(),
                            Forms\Components\Toggle::make('is_active')
                                ->default(true)
                                ->required(),
                            Forms\Components\Select::make('index_status')
                                ->options([
                                    'index' => 'Index',
                                    'noindex' => 'No Index',
                                ])
                                ->default('index')
                                ->required(),
                            Forms\Components\Select::make('follow_status')
                                ->options([
                                    'follow' => 'Follow',
                                    'nofollow' => 'No Follow',
                                ])
                                ->default('follow')
                                ->required(),
                            Forms\Components\Select::make('content_type')
                                ->options([
                                    'programs' => 'Programs',
                                    'jobs' => 'Jobs',
                                    'mixed' => 'Mixed',
                                ])
                                ->default('mixed')
                                ->required(),
                            Forms\Components\TextInput::make('items_per_page')
                                ->numeric()
                                ->default(20),
                            Forms\Components\KeyValue::make('target_filters')
                                ->label('Target Filters'),
                        ]),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('views')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListSeoLandings::route('/'),
            'create' => Pages\CreateSeoLanding::route('/create'),
            'edit' => Pages\EditSeoLanding::route('/{record}/edit'),
        ];
    }
}
