<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriptionResource\Pages;
use App\Models\NewsletterSubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterSubscriptionResource extends Resource
{
    protected static ?string $model = NewsletterSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Subscribers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscriber Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Subscription Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'unsubscribed' => 'Unsubscribed',
                                'bounced' => 'Bounced',
                                'pending' => 'Pending',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Select::make('frequency')
                            ->options([
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                                'yearly' => 'Yearly'
                            ])
                            ->default('weekly')
                            ->required(),
                        Forms\Components\TagsInput::make('tags')
                            ->label('Tags')
                            ->helperText('Used for segmentation and targeting.'),
                        Forms\Components\KeyValue::make('preferences')
                            ->label('Preferences')
                            ->helperText('e.g., key: topics, value: tech,business'),
                    ]),

                Forms\Components\Section::make('Metadata')
                    ->description('This information is typically collected automatically.')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('source'),
                        Forms\Components\DateTimePicker::make('verified_at'),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address'),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Verified On'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListNewsletterSubscriptions::route('/'),
            'create' => Pages\CreateNewsletterSubscription::route('/create'),
            'edit' => Pages\EditNewsletterSubscription::route('/{record}/edit'),
        ];
    }
}
