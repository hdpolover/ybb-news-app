<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MediaResource\Pages;
use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Facades\Filament;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Media';

    protected static ?string $modelLabel = 'Media';

    protected static ?string $pluralModelLabel = 'Media';

    public static function getEloquentQuery(): Builder
    {
        $tenantId = session('current_tenant_id');
        $query = parent::getEloquentQuery();
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        return $query;
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        // All authenticated users can upload media
        return $user !== null;
    }

    public static function canEdit($record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Tenant admins and editors can edit any media
        if ($user->hasRole(['Tenant Admin', 'Editor'])) {
            return true;
        }
        
        // Others can only edit media they uploaded
        return $record->uploaded_by === $user->id;
    }

    public static function canDelete($record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Tenant admins and editors can delete any media
        if ($user->hasRole(['Tenant Admin', 'Editor'])) {
            return true;
        }
        
        // Others can only delete media they uploaded
        return $record->uploaded_by === $user->id;
    }

    public static function canDeleteAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        return $user && $user->hasRole(['Tenant Admin', 'Editor']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('File Upload')
                    ->schema([
                        Forms\Components\FileUpload::make('file_name')
                            ->label('File')
                            ->disk('public')
                            ->directory('media')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, TemporaryUploadedFile $state) {
                                $originalName = $state->getClientOriginalName();
                                $set('name', pathinfo($originalName, PATHINFO_FILENAME));
                                $set('mime_type', $state->getMimeType());
                                $set('size', $state->getSize());
                            })
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('File Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Hidden::make('mime_type'),
                        Forms\Components\Hidden::make('size'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Defaults to the original file name.'),
                        Forms\Components\TextInput::make('collection_name')
                            ->label('Collection')
                            ->maxLength(255)
                            ->default('default')
                            ->helperText('Group files into collections (e.g., "featured", "gallery")'),
                        Forms\Components\TextInput::make('alt_text')
                            ->label('Alt Text')
                            ->maxLength(255)
                            ->helperText('For accessibility and SEO'),
                        Forms\Components\Textarea::make('caption')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_name')
                    ->label('Preview')
                    ->disk('public')
                    ->size(60),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match (explode('/', $state)[0]) {
                        'image' => 'success',
                        'video' => 'info',
                        'audio' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('size')
                    ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 2) . ' KB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('collection_name')
                    ->label('Collection')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mime_type')
                    ->options([
                        'image/jpeg' => 'JPEG',
                        'image/png' => 'PNG',
                        'image/gif' => 'GIF',
                        'image/svg+xml' => 'SVG',
                        'application/pdf' => 'PDF',
                    ])
                    ->label('File Type'),
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
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
