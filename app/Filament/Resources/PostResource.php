<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Main Content')
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('content')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('kind')
                            ->options([
                                'article' => 'Article',
                                'job' => 'Job',
                                'program' => 'Program',
                            ])
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'scheduled' => 'Scheduled',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at'),
                    ])->columns(2),

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

                Forms\Components\Section::make('Meta & Relations')
                    ->schema([
                        Forms\Components\Select::make('terms')
                            ->relationship('terms', 'name')
                            ->multiple()
                            ->preload(),
                        Forms\Components\Select::make('created_by')
                            ->relationship('author', 'name')
                            ->required(),
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
                Tables\Columns\TextColumn::make('kind')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
