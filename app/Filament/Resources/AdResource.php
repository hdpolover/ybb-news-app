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
                Forms\Components\Section::make('Campaign Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'banner' => 'Banner',
                                'text' => 'Text',
                                'video' => 'Video',
                            ])
                            ->live()
                            ->required(),
                        Forms\Components\CheckboxList::make('placement')
                            ->options([
                                'sidebar_top' => 'Sidebar - Atas',
                                'sidebar_bottom' => 'Sidebar - Bawah',
                                'post_top' => 'Artikel - Atas',
                                'post_middle' => 'Artikel - Tengah (setelah paragraf ke-3)',
                                'post_bottom' => 'Artikel - Bawah',
                                'header_banner' => 'Banner Header',
                                'footer_banner' => 'Banner Footer',
                            ])
                            ->label('Placement')
                            ->required()
                            ->columns(2),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'paused' => 'Paused',
                                'archived' => 'Archived',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Ad Content')
                    ->schema([
                        // Banner Type Fields
                        Forms\Components\FileUpload::make('content.image_url')
                            ->disk('public')
                            ->directory('ads')
                            ->label('Banner Image')
                            ->visible(fn(Get $get) => $get('type') === 'banner'),
                        Forms\Components\TextInput::make('content.target_link')
                            ->label('Target Link (for Banner/Text)')
                            ->visible(fn(Get $get) => in_array($get('type'), ['banner', 'text'])),

                        // Text Type Fields
                        Forms\Components\TextInput::make('content.headline')
                            ->label('Headline')
                            ->visible(fn(Get $get) => $get('type') === 'text'),
                        Forms\Components\Textarea::make('content.description')
                            ->label('Ad Text / Body')
                            ->visible(fn(Get $get) => $get('type') === 'text'),

                        // Video Type Fields
                        Forms\Components\TextInput::make('content.video_url')
                            ->label('Video URL')
                            ->helperText('e.g., YouTube or Vimeo URL')
                            ->visible(fn(Get $get) => $get('type') === 'video'),
                    ]),

                Forms\Components\Section::make('Delivery & Performance')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('priority')
                            ->numeric()
                            ->default(0)
                            ->helperText('Ads with higher numbers are shown more often.'),
                        Forms\Components\TextInput::make('max_impressions')
                            ->label('Max Impressions')
                            ->numeric()
                            ->helperText('Leave blank for unlimited impressions.'),
                        Forms\Components\TextInput::make('max_clicks')
                            ->label('Max Clicks')
                            ->numeric()
                            ->helperText('Leave blank for unlimited clicks.'),
                        Forms\Components\DateTimePicker::make('start_date'),
                        Forms\Components\DateTimePicker::make('end_date'),
                    ]),

                Forms\Components\Section::make('Advanced Settings')
                    ->collapsible() // Opsi agar bisa disembunyikan
                    ->schema([
                        Forms\Components\KeyValue::make('targeting')
                            ->label('Targeting Rules')
                            ->helperText('e.g., key: country, value: Indonesia'),
                        Forms\Components\KeyValue::make('settings')
                            ->label('Additional Settings'),
                    ]),
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
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }
}
