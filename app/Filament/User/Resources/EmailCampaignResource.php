<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\EmailCampaignResource\Pages;
use App\Models\EmailCampaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmailCampaignResource extends Resource
{
    protected static ?string $model = EmailCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 2;

    protected static ?string $label = 'Email Campaign';

    protected static ?string $pluralLabel = 'Email Campaigns';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Campaign Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                            
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('The subject line that recipients will see'),
                            
                        Forms\Components\Textarea::make('preview_text')
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('Preview text shown in email clients'),
                            
                        Forms\Components\Select::make('type')
                            ->options([
                                'newsletter' => 'Newsletter',
                                'announcement' => 'Announcement',
                                'promotional' => 'Promotional',
                                'automated' => 'Automated',
                            ])
                            ->required()
                            ->default('newsletter'),
                            
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'sending' => 'Sending',
                                'sent' => 'Sent',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('draft'),
                            
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Schedule For')
                            ->nullable()
                            ->minDate(now())
                            ->helperText('Leave empty to send immediately'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\Select::make('template')
                            ->options([
                                'default' => 'Default Template',
                                'newsletter' => 'Newsletter Template',
                                'promotional' => 'Promotional Template',
                                'minimal' => 'Minimal Template',
                            ])
                            ->default('default')
                            ->required()
                            ->reactive()
                            ->columnSpanFull(),
                            
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('email-attachments')
                            ->helperText('Use the rich editor to design your email content'),
                    ]),
                    
                Forms\Components\Section::make('Recipients')
                    ->schema([
                        Forms\Components\KeyValue::make('recipient_criteria')
                            ->label('Recipient Criteria')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->helperText('Define criteria to filter recipients (e.g., role: subscriber)')
                            ->columnSpanFull(),
                            
                        Forms\Components\TextInput::make('estimated_recipients')
                            ->numeric()
                            ->disabled()
                            ->helperText('Estimated number of recipients based on criteria'),
                    ])
                    ->collapsible(),
                    
                Forms\Components\Section::make('Advanced Settings')
                    ->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('Additional Settings')
                            ->keyLabel('Setting')
                            ->valueLabel('Value')
                            ->helperText('Custom settings for this campaign')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'primary' => 'newsletter',
                        'warning' => 'announcement',
                        'success' => 'promotional',
                        'info' => 'automated',
                    ]),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'scheduled',
                        'info' => 'sending',
                        'success' => 'sent',
                        'danger' => 'cancelled',
                    ]),
                    
                Tables\Columns\TextColumn::make('estimated_recipients')
                    ->label('Recipients')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('open_rate')
                    ->label('Open Rate')
                    ->formatStateUsing(fn ($state) => $state ? round($state, 2) . '%' : '-')
                    ->sortable()
                    ->color(fn ($state) => $state && $state >= 20 ? 'success' : ($state && $state >= 10 ? 'warning' : 'gray'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('click_rate')
                    ->label('Click Rate')
                    ->formatStateUsing(fn ($state) => $state ? round($state, 2) . '%' : '-')
                    ->sortable()
                    ->color(fn ($state) => $state && $state >= 5 ? 'success' : ($state && $state >= 2 ? 'warning' : 'gray'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sending' => 'Sending',
                        'sent' => 'Sent',
                        'cancelled' => 'Cancelled',
                    ]),
                    
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'newsletter' => 'Newsletter',
                        'announcement' => 'Announcement',
                        'promotional' => 'Promotional',
                        'automated' => 'Automated',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (EmailCampaign $record) {
                        // TODO: Implement sending logic
                        $record->update([
                            'status' => 'sending',
                            'sent_at' => now(),
                        ]);
                    })
                    ->visible(fn (EmailCampaign $record) => $record->status === 'draft' || $record->status === 'scheduled'),
                    
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->action(function (EmailCampaign $record) {
                        $newCampaign = $record->replicate();
                        $newCampaign->name = $record->name . ' (Copy)';
                        $newCampaign->status = 'draft';
                        $newCampaign->sent_at = null;
                        $newCampaign->save();
                    }),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (EmailCampaign $record) => $record->status === 'draft' || $record->status === 'cancelled'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', session('tenant_id'));
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
