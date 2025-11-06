<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Tenant Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tenant Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'pending' => 'Pending',
                                'suspended' => 'Suspended',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Tabs::make('Advanced Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Features')
                            ->schema([
                                Forms\Components\CheckboxList::make('enabled_features')
                                    ->label('Enabled Features')
                                    ->options([
                                        'programs' => 'Programs Management',
                                        'jobs' => 'Jobs Management',
                                        'news' => 'News Management',
                                        'seo' => 'SEO Settings',
                                        'ads' => 'Ads Management',
                                        'newsletter' => 'Newsletter Service',
                                    ])
                                    ->columns(2),
                                Forms\Components\KeyValue::make('settings')
                                    ->label('Additional Settings'),
                            ])->columns(1),

                        Forms\Components\Tabs\Tab::make('Branding')
                            ->schema([
                                Forms\Components\TextInput::make('logo_url')
                                    ->label('Logo URL'),
                                Forms\Components\TextInput::make('favicon_url')
                                    ->label('Favicon URL'),
                                Forms\Components\ColorPicker::make('primary_color')
                                    ->label('Primary Color'),
                                Forms\Components\ColorPicker::make('secondary_color')
                                    ->label('Secondary Color'),
                                Forms\Components\ColorPicker::make('accent_color')
                                    ->label('Accent Color'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('SEO & Analytics')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Meta Title'),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('og_image_url')
                                    ->label('Open Graph Image URL'),
                                Forms\Components\TextInput::make('google_analytics_id')
                                    ->label('Google Analytics ID'),
                                Forms\Components\TextInput::make('google_adsense_id')
                                    ->label('Google AdSense ID'),
                                Forms\Components\TextInput::make('google_tag_manager_id')
                                    ->label('Google Tag Manager ID'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Email Settings')
                            ->schema([
                                Forms\Components\TextInput::make('email_from_name')
                                    ->label('Email From Name'),
                                Forms\Components\TextInput::make('email_from_address')
                                    ->label('Email From Address')
                                    ->email(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Legal & Privacy')
                            ->schema([
                                Forms\Components\Toggle::make('gdpr_enabled')
                                    ->label('Enable GDPR')
                                    ->inline(false),
                                Forms\Components\Toggle::make('ccpa_enabled')
                                    ->label('Enable CCPA')
                                    ->inline(false),
                                Forms\Components\TextInput::make('privacy_policy_url')
                                    ->label('Privacy Policy URL'),
                                Forms\Components\TextInput::make('terms_of_service_url')
                                    ->label('Terms of Service URL'),
                            ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tenant Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('posts_count')
                    ->label('Posts')
                    ->counts('posts')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'suspended' => 'Suspended',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'view' => Pages\ViewTenant::route('/{record}'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
