<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdResource\Pages;
use App\Models\Ad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('placement')
                    ->required()
                    ->helperText('e.g., sidebar_top, post_bottom'),
                Forms\Components\Select::make('type')
                    ->options([
                        'banner' => 'Banner',
                        'text' => 'Text',
                        'video' => 'Video',
                    ])
                    ->live()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'archived' => 'Archived',
                    ])
                    ->default('active')
                    ->required(),

                Forms\Components\Section::make('Ad Content')
                    ->schema([
                        Forms\Components\FileUpload::make('content.image_url')
                            ->label('Banner Image')
                            ->visible(fn(Get $get) => $get('type') === 'banner'),
                        Forms\Components\TextInput::make('content.target_link')
                            ->label('Target Link')
                            ->visible(fn(Get $get) => $get('type') === 'banner'),
                        Forms\Components\TextInput::make('content.headline')
                            ->label('Headline')
                            ->visible(fn(Get $get) => $get('type') === 'text'),
                        Forms\Components\Textarea::make('content.description')
                            ->label('Description')
                            ->visible(fn(Get $get) => $get('type') === 'text'),
                        Forms\Components\TextInput::make('content.video_url')
                            ->label('Video URL')
                            ->visible(fn(Get $get) => $get('type') === 'video'),
                    ]),

                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\DateTimePicker::make('start_date'),
                Forms\Components\DateTimePicker::make('end_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('placement'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('current_impressions')
                    ->label('Impressions')
                    ->numeric(),
                Tables\Columns\TextColumn::make('current_clicks')
                    ->label('Clicks')
                    ->numeric(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }
}
