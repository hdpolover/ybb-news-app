<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('File Upload')
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required(),
                        Forms\Components\FileUpload::make('file_name')
                            ->label('File')
                            ->disk('public')
                            ->directory('media')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, TemporaryUploadedFile $state) {
                                // Mengisi field lain secara otomatis setelah file di-upload
                                $originalName = $state->getClientOriginalName();
                                $set('name', pathinfo($originalName, PATHINFO_FILENAME)); // Nama file tanpa ekstensi
                                $set('mime_type', $state->getMimeType());
                                $set('size', $state->getSize());
                            }),
                    ]),
                Forms\Components\Section::make('File Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->helperText('Defaults to the original file name.')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('collection_name')
                            ->helperText('e.g., avatars, post-thumbnails'),
                        Forms\Components\TextInput::make('alt_text')
                            ->label('Alt Text')
                            ->helperText('Important for SEO and accessibility.')
                            ->maxLength(255),
                        Forms\Components\Select::make('post_id')
                            ->relationship('post', 'title')
                            ->searchable()
                            ->label('Associated Post'),
                        Forms\Components\Textarea::make('caption')
                            ->columnSpanFull(),
                        // Field tersembunyi untuk menyimpan metadata
                        Forms\Components\Hidden::make('mime_type'),
                        Forms\Components\Hidden::make('size'),
                    ]),
                Forms\Components\Section::make('Advanced')
                    ->collapsible()
                    ->schema([
                        Forms\Components\KeyValue::make('custom_properties')
                            ->label('Custom Properties'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_name')
                    ->disk('public')
                    ->label('Preview'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Size')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(?int $state): string => $state ? number_format($state / 1024, 1) . ' KB' : '0 KB'),
                Tables\Columns\TextColumn::make('mime_type'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
