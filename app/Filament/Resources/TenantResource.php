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

    protected static ?string $navigationGroup = 'Core';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')->tabs([
                    Forms\Components\Tabs\Tab::make('Umum')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Tenant')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('domain')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('status')
                                ->options([
                                    'active' => 'Aktif',
                                    'suspended' => 'Ditangguhkan',
                                    'pending' => 'Tertunda',
                                ])
                                ->required(),
                            Forms\Components\Textarea::make('description')
                                ->label('Deskripsi')
                                ->columnSpanFull(),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Branding')
                        ->schema([
                            Forms\Components\TextInput::make('logo_url')
                                ->label('URL Logo'),
                            Forms\Components\TextInput::make('favicon_url')
                                ->label('URL Favicon'),
                            Forms\Components\ColorPicker::make('primary_color')
                                ->label('Warna Primer'),
                            Forms\Components\ColorPicker::make('secondary_color')
                                ->label('Warna Sekunder'),
                            Forms\Components\ColorPicker::make('accent_color')
                                ->label('Warna Aksen'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('SEO & Analytics')
                        ->schema([
                            Forms\Components\TextInput::make('meta_title')
                                ->label('Meta Title'),
                            Forms\Components\Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('og_image_url')
                                ->label('URL Open Graph Image'),
                            Forms\Components\TextInput::make('google_analytics_id')
                                ->label('Google Analytics ID'),
                            Forms\Components\TextInput::make('google_adsense_id')
                                ->label('Google AdSense ID'),
                            Forms\Components\TextInput::make('google_tag_manager_id')
                                ->label('Google Tag Manager ID'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Pengaturan')
                        ->schema([
                            Forms\Components\TextInput::make('email_from_name')
                                ->label('Nama Pengirim Email'),
                            Forms\Components\TextInput::make('email_from_address')
                                ->label('Alamat Email Pengirim')
                                ->email(),
                            Forms\Components\CheckboxList::make('enabled_features')
                                ->label('Fitur yang Diaktifkan')
                                ->options([
                                    'programs' => 'Manajemen Program',
                                    'jobs' => 'Manajemen Lowongan Kerja',
                                    'news' => 'Manajemen Berita',
                                    'seo' => 'Pengaturan SEO Lanjutan',
                                    'ads' => 'Manajemen Iklan/AdSense',
                                    'newsletter' => 'Layanan Newsletter',
                                ])
                                ->columns(2)
                                ->required(),
                            Forms\Components\KeyValue::make('settings')
                                ->label('Pengaturan Tambahan'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Hukum & Privasi')
                        ->schema([
                            Forms\Components\Toggle::make('gdpr_enabled')
                                ->label('Aktifkan GDPR')
                                ->inline(false),
                            Forms\Components\Toggle::make('ccpa_enabled')
                                ->label('Aktifkan CCPA')
                                ->inline(false),
                            Forms\Components\TextInput::make('privacy_policy_url')
                                ->label('URL Kebijakan Privasi'),
                            Forms\Components\TextInput::make('terms_of_service_url')
                                ->label('URL Ketentuan Layanan'),
                        ])->columns(2),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->copyable()
                    ->copyMessage('ID telah disalin'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('domain')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
