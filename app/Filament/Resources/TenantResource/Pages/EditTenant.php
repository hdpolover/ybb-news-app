<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
// Import yang diperlukan
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Actions as FormActions;
use Filament\Forms\Components\Actions\Action as FormComponentAction;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * 1. Sembunyikan tombol di footer
     */
    protected function getFormActions(): array
    {
        return [];
    }

    /**
     * 2. Definisikan ulang seluruh form di sini (disalin dari TenantResource.php)
     */
    public function form(Form $form): Form
    {
        // Skema ini disalin dari TenantResource.php
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')->tabs([
                    Forms\Components\Tabs\Tab::make('Umum')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Tenant')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('domain') // <-- Ini yang saya lupakan
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
                                ->label('Warna Primer')
                                ->required(),
                            Forms\Components\ColorPicker::make('secondary_color')
                                ->label('Warna Sekunder')
                                ->required(),
                            Forms\Components\ColorPicker::make('accent_color')
                                ->label('Warna Aksen')
                                ->required(),
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
                            Forms\Components\TagsInput::make('enabled_features')
                                ->label('Fitur yang Diaktifkan'),
                            Forms\Components\KeyValue::make('settings')
                                ->label('Pengaturan Tambahan'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Hukum & Privasi') // <-- Tab Terakhir
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

                            // <-- Tombol Aksi Dimasukkan DI SINI
                            FormActions::make([
                                FormComponentAction::make('save')
                                    ->label('Save Changes')
                                    ->action('save'),
                            ])->columnSpanFull()
                        ])->columns(2),
                ])->columnSpanFull()
            ]);
    }
}
