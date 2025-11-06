<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    protected static ?string $label = 'Audit Log';

    protected static ?string $pluralLabel = 'Audit Logs';

    public static function canCreate(): bool
    {
        return false; // Audit logs are created automatically
    }

    public static function canEdit($record): bool
    {
        return false; // Audit logs are immutable
    }

    public static function canDelete($record): bool
    {
        return false; // Audit logs should not be deleted
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->default('System')
                    ->formatStateUsing(fn ($state) => $state ?? 'System'),
                    
                Tables\Columns\TextColumn::make('event')
                    ->badge()
                    ->colors([
                        'success' => fn ($state) => in_array($state, ['created', 'published', 'approved']),
                        'warning' => fn ($state) => in_array($state, ['updated', 'restored']),
                        'danger' => fn ($state) => in_array($state, ['deleted', 'rejected', 'unpublished']),
                        'info' => fn ($state) => in_array($state, ['viewed']),
                        'gray' => fn ($state) => !in_array($state, ['created', 'published', 'approved', 'updated', 'restored', 'deleted', 'rejected', 'unpublished', 'viewed']),
                    ])
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Resource')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),
                    
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'viewed' => 'Viewed',
                        'published' => 'Published',
                        'unpublished' => 'Unpublished',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'restored' => 'Restored',
                    ]),
                    
                Tables\Filters\SelectFilter::make('auditable_type')
                    ->label('Resource Type')
                    ->options([
                        'App\Models\Post' => 'Post',
                        'App\Models\User' => 'User',
                        'App\Models\Media' => 'Media',
                        'App\Models\Term' => 'Term',
                        'App\Models\Ad' => 'Ad',
                        'App\Models\EmailCampaign' => 'Email Campaign',
                    ]),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn ($record) => 'Audit Log Details')
                    ->modalContent(fn ($record) => view('filament.user.resources.audit-log-resource.view-audit-log', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', session('current_tenant_id'))
            ->with(['user']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $tenantRole = $user?->getTenantRole(session('current_tenant_id'));
        
        // Only tenant_admin can view audit logs
        return $tenantRole === 'tenant_admin';
    }
}
