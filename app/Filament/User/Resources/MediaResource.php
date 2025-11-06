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
                        Forms\Components\Select::make('folder')
                            ->label('Folder')
                            ->options(function () {
                                $tenantId = session('current_tenant_id');
                                $folders = Media::where('tenant_id', $tenantId)
                                    ->whereNotNull('folder')
                                    ->distinct()
                                    ->pluck('folder', 'folder');
                                return $folders;
                            })
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('folder')
                                    ->label('New Folder Name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return $data['folder'];
                            })
                            ->helperText('Organize media into folders'),
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
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('folder')
                    ->label('Folder')
                    ->badge()
                    ->color('gray')
                    ->default('Uncategorized')
                    ->sortable()
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Used')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state < 5 => 'success',
                        $state < 10 => 'warning',
                        default => 'danger',
                    })
                    ->sortable()
                    ->tooltip(fn ($record) => $record->usage_count > 0 ? "Used in {$record->usage_count} place(s)" : 'Not used'),
                Tables\Columns\TextColumn::make('collection_name')
                    ->label('Collection')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Uploaded By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('folder')
                    ->options(function () {
                        $tenantId = session('current_tenant_id');
                        $folders = Media::where('tenant_id', $tenantId)
                            ->whereNotNull('folder')
                            ->distinct()
                            ->pluck('folder', 'folder');
                        return ['uncategorized' => 'Uncategorized'] + $folders->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            if ($data['value'] === 'uncategorized') {
                                return $query->whereNull('folder');
                            }
                            return $query->where('folder', $data['value']);
                        }
                        return $query;
                    })
                    ->label('Folder'),
                Tables\Filters\SelectFilter::make('mime_type')
                    ->options([
                        'image' => 'Images',
                        'video' => 'Videos',
                        'audio' => 'Audio',
                        'application/pdf' => 'PDFs',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value']) && $data['value'] !== 'application/pdf') {
                            return $query->where('mime_type', 'like', $data['value'] . '%');
                        }
                        if ($data['value'] === 'application/pdf') {
                            return $query->where('mime_type', 'application/pdf');
                        }
                        return $query;
                    })
                    ->label('File Type'),
                Tables\Filters\Filter::make('unused')
                    ->label('Unused Only')
                    ->query(fn (Builder $query): Builder => $query->where('usage_count', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        // Check if media is in use
                        if ($record->usage_count > 0) {
                            throw new \Exception("Cannot delete media that is currently in use ({$record->usage_count} place(s)).");
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('moveToFolder')
                        ->label('Move to Folder')
                        ->icon('heroicon-o-folder')
                        ->form([
                            Forms\Components\Select::make('folder')
                                ->label('Folder')
                                ->options(function () {
                                    $tenantId = session('current_tenant_id');
                                    $folders = Media::where('tenant_id', $tenantId)
                                        ->whereNotNull('folder')
                                        ->distinct()
                                        ->pluck('folder', 'folder');
                                    return $folders;
                                })
                                ->searchable()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('folder')
                                        ->label('New Folder Name')
                                        ->required()
                                        ->maxLength(255),
                                ])
                                ->createOptionUsing(function (array $data) {
                                    return $data['folder'];
                                })
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(fn ($record) => $record->update(['folder' => $data['folder']]));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Media moved')
                                ->body('Selected media has been moved to the folder.')
                        ),
                    Tables\Actions\BulkAction::make('updateMetadata')
                        ->label('Update Metadata')
                        ->icon('heroicon-o-pencil-square')
                        ->form([
                            Forms\Components\TextInput::make('alt_text')
                                ->label('Alt Text')
                                ->maxLength(255),
                            Forms\Components\Textarea::make('caption')
                                ->label('Caption')
                                ->rows(2),
                        ])
                        ->action(function ($records, array $data) {
                            $updates = array_filter($data); // Remove empty values
                            if (!empty($updates)) {
                                $records->each(fn ($record) => $record->update($updates));
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotification(
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Metadata updated')
                                ->body('Selected media metadata has been updated.')
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            $inUse = $records->filter(fn ($record) => $record->usage_count > 0);
                            if ($inUse->count() > 0) {
                                throw new \Exception("Cannot delete {$inUse->count()} media file(s) that are currently in use.");
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
