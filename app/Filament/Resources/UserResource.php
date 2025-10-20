<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Access Control';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Profile')
                    ->columns(2)
                    ->schema([
                        Forms\Components\FileUpload::make('avatar_url')
                            ->disk('public')
                            ->directory('avatar')
                            ->label('Avatar')
                            ->image()
                            ->avatar(),
                        Forms\Components\Textarea::make('bio')
                            ->rows(3),
                    ]),
                Forms\Components\Section::make('Account Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        // --- TAMBAHKAN FIELD INI ---
                        Forms\Components\Select::make('roles')
                            ->multiple()
                            ->relationship('roles', 'name') // Menggunakan relasi 'roles' (dari Spatie)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        // --- SELESAI ---

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->helperText('Leave blank to keep the current password.')
                            ->columnSpanFull(), // Dibuat full-width
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ]),
                Forms\Components\Section::make('Advanced Settings')
                    ->collapsible()
                    ->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('User Settings'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->disk('public')
                    ->label('Avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                // --- TAMBAHKAN KOLOM INI ---
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge() // Tampilkan sebagai badge
                    ->searchable(),
                // --- SELESAI ---

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
