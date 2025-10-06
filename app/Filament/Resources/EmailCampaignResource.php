<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailCampaignResource\Pages;
use App\Models\EmailCampaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailCampaignResource extends Resource
{
    protected static ?string $model = EmailCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Campaign Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('preview_text')
                            ->helperText('This text appears after the subject line in most email clients.')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\TextInput::make('template')
                            ->helperText('Optional: Specify a template file name.'),
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Scheduling & Recipients')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'newsletter' => 'Newsletter',
                                'digest' => 'Digest',
                                'announcement' => 'Announcement',
                                'promotional' => 'Promotional',
                            ])
                            ->default('newsletter')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'sending' => 'Sending',
                                'sent' => 'Sent',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->helperText('Leave blank to send immediately (if status is not draft).'),
                        Forms\Components\KeyValue::make('recipient_criteria')
                            ->label('Recipient Criteria')
                            ->helperText('Define rules to segment your audience.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Advanced Settings')
                    ->collapsible()
                    ->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('Additional Settings'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('open_rate')
                    ->label('Open %')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('click_rate')
                    ->label('Click %')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
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
            'index' => Pages\ListEmailCampaigns::route('/'),
            'create' => Pages\CreateEmailCampaign::route('/create'),
            'edit' => Pages\EditEmailCampaign::route('/{record}/edit'),
        ];
    }
}
