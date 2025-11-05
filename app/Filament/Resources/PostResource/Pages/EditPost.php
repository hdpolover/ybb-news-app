<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions as FilamentActions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Actions as FormActions;
use Filament\Forms\Components\Actions\Action as FormComponentAction;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\DeleteAction::make(),
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
     * 2. Definisikan ulang seluruh form di sini
     */
    public function form(Form $form): Form
    {
        // Skema disalin dari PostResource.php
        return $form
            ->schema([
                Tabs::make('PostTabs')->tabs([
                    Tabs\Tab::make('Content')
                        ->schema([
                            // ... (Sama seperti di Create) ...
                            Forms\Components\TextInput::make('title')
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
                                ->rows(3)
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('content')
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('SEO')
                        ->schema([
                            // ... (Sama seperti di Create) ...
                            Forms\Components\TextInput::make('meta_title'),
                            Forms\Components\Textarea::make('meta_description'),
                            Forms\Components\FileUpload::make('og_image_url')
                                ->label('Social Media Image (OG)')
                                ->image()
                                ->disk('public')
                                ->directory('post-og-images'),
                            Forms\Components\TextInput::make('canonical_url')
                                ->label('Canonical URL')
                                ->url(),
                        ]),
                    Tabs\Tab::make('Details & Relations')
                        ->schema([
                            // ... (Sama seperti di Create) ...
                            Forms\Components\Select::make('tenant_id')
                                ->relationship('tenant', 'name')
                                ->required(),
                            Forms\Components\Select::make('kind')
                                ->options([
                                    'page' => 'Page',
                                    'news' => 'News',
                                    'guide' => 'Guide',
                                    'program' => 'Program',
                                    'job' => 'Job',
                                ])
                                ->live()
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'review' => 'In Review',
                                    'scheduled' => 'Scheduled',
                                    'published' => 'Published',
                                    'archived' => 'Archived',
                                ])
                                ->live()
                                ->required(),
                            Forms\Components\DateTimePicker::make('published_at'),
                            Forms\Components\DateTimePicker::make('scheduled_at')
                                ->visible(fn(Get $get): bool => $get('status') === 'scheduled'),
                            Forms\Components\Select::make('created_by')
                                ->relationship('author', 'name')
                                ->required(),
                            Forms\Components\Select::make('terms')
                                ->label('Categories')
                                ->relationship(
                                    name: 'terms',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn(Builder $query) => $query->where('type', 'category')
                                )
                                ->multiple()
                                ->preload()
                                ->searchable(),

                            // <-- Tombol Aksi Dimasukkan DI SINI (di dalam Tab terakhir)
                            FormActions::make([
                                FormComponentAction::make('save')
                                    ->label('Save Changes')
                                    ->action('save'),
                            ])->columnSpanFull()

                        ]),
                ])->columnSpanFull(),

                // ... (Section 'Job Details' dan 'Program Details' sama persis) ...
                Forms\Components\Section::make('Job Details')
                    ->schema([
                        Forms\Components\TextInput::make('job.company_name'),
                        Forms\Components\TextInput::make('job.employment_type'),
                        Forms\Components\TextInput::make('job.location_city'),
                        Forms\Components\TextInput::make('job.min_salary')->numeric(),
                        Forms\Components\TextInput::make('job.max_salary')->numeric(),
                        Forms\Components\TextInput::make('job.apply_url')->url(),
                    ])
                    ->visible(fn(Get $get): bool => $get('kind') === 'job'),

                Forms\Components\Section::make('Program Details')
                    ->schema([
                        Forms\Components\TextInput::make('program.organizer_name'),
                        Forms\Components\TextInput::make('program.location_text'),
                        Forms\Components\TextInput::make('program.funding_type'),
                        Forms\Components\TextInput::make('program.apply_url')->url(),
                    ])
                    ->visible(fn(Get $get): bool => $get('kind') === 'program'),
            ]);
    }
}
