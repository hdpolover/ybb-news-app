<?php

namespace App\Filament\Resources\SeoLandingResource\Pages;

use App\Filament\Resources\SeoLandingResource;
use Filament\Resources\Pages\CreateRecord;
// Import yang diperlukan
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Actions as FormActions;
use Filament\Forms\Components\Actions\Action as FormComponentAction;

class CreateSeoLanding extends CreateRecord
{
    protected static string $resource = SeoLandingResource::class;

    /**
     * 1. Kosongkan method ini untuk menyembunyikan tombol di footer
     */
    protected function getFormActions(): array
    {
        return [];
    }

    /**
     * 2. Definisikan ulang seluruh form di sini (disalin dari SeoLandingResource)
     */
    public function form(Form $form): Form
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
                    Tabs\Tab::make('Page Settings') // <-- Tab Terakhir
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

                            // <-- Tombol Aksi Dimasukkan DI SINI
                            FormActions::make([
                                FormComponentAction::make('create')
                                    ->label('Save')
                                    ->action('create'),
                                FormComponentAction::make('createAnother')
                                    ->label('Save & create another')
                                    ->action('createAnother'),
                            ])->columnSpanFull()

                        ]),
                ])->columnSpanFull()
            ]);
    }
}
