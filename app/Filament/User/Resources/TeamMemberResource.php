<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\TeamMemberResource\Pages;
use App\Models\User;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;

class TeamMemberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Team Members';

    protected static ?string $modelLabel = 'Team Member';

    protected static ?string $pluralModelLabel = 'Team Members';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $tenantId = session('tenant_id');
        
        // Get users who belong to the current tenant
        return parent::getEloquentQuery()
            ->whereHas('tenants', function (Builder $query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->with(['tenants' => function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            }]);
    }

    public static function form(Form $form): Form
    {
        $tenantId = session('tenant_id');
        
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Full Name'),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Email Address'),
                        
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->label('Password')
                            ->helperText(fn (string $context): string => 
                                $context === 'create' 
                                    ? 'A temporary password will be sent to the user\'s email.' 
                                    : 'Leave blank to keep the current password.'
                            ),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Role Assignment')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                'tenant_admin' => 'Tenant Admin - Full access to tenant settings',
                                'editor' => 'Editor - Can publish and edit all content',
                                'author' => 'Author - Can create and edit own content',
                                'contributor' => 'Contributor - Can create content for review',
                            ])
                            ->default('author')
                            ->required()
                            ->native(false)
                            ->helperText('Select the role for this team member within your organization.'),
                        
                        Forms\Components\Toggle::make('is_default')
                            ->label('Set as Default Tenant')
                            ->helperText('Make this tenant the default when the user logs in.')
                            ->default(false)
                            ->hidden(fn (string $context) => $context === 'create'),
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Invited On')
                            ->content(fn (User $record): string => $record->created_at?->format('F j, Y g:i A') ?? 'N/A'),
                        
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last Updated')
                            ->content(fn (User $record): string => $record->updated_at?->format('F j, Y g:i A') ?? 'N/A'),
                    ])
                    ->columns(2)
                    ->hidden(fn (string $context) => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $tenantId = session('tenant_id');
        
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Name')
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email')
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                
                Tables\Columns\BadgeColumn::make('pivot.role')
                    ->label('Role')
                    ->colors([
                        'danger' => 'tenant_admin',
                        'warning' => 'editor',
                        'success' => 'author',
                        'secondary' => 'contributor',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'tenant_admin' => 'Admin',
                        'editor' => 'Editor',
                        'author' => 'Author',
                        'contributor' => 'Contributor',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\IconColumn::make('pivot.is_default')
                    ->label('Default Tenant')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('tenants_count')
                    ->counts('tenants')
                    ->label('Total Tenants')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'tenant_admin' => 'Admin',
                        'editor' => 'Editor',
                        'author' => 'Author',
                        'contributor' => 'Contributor',
                    ])
                    ->query(function (Builder $query, array $data) use ($tenantId) {
                        if (filled($data['value'])) {
                            $query->whereHas('tenants', function (Builder $q) use ($tenantId, $data) {
                                $q->where('tenant_id', $tenantId)
                                  ->wherePivot('role', $data['value']);
                            });
                        }
                    }),
                
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Default Tenant')
                    ->placeholder('All members')
                    ->trueLabel('This is default')
                    ->falseLabel('Not default')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('tenants', function (Builder $q) use ($tenantId) {
                            $q->where('tenant_id', $tenantId)->wherePivot('is_default', true);
                        }),
                        false: fn (Builder $query) => $query->whereHas('tenants', function (Builder $q) use ($tenantId) {
                            $q->where('tenant_id', $tenantId)->wherePivot('is_default', false);
                        }),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('changeRole')
                    ->label('Change Role')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('role')
                            ->label('New Role')
                            ->options([
                                'tenant_admin' => 'Tenant Admin',
                                'editor' => 'Editor',
                                'author' => 'Author',
                                'contributor' => 'Contributor',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->action(function (User $record, array $data) use ($tenantId) {
                        $record->tenants()->updateExistingPivot($tenantId, [
                            'role' => $data['role'],
                        ]);
                        
                        Notification::make()
                            ->title('Role updated successfully')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Change Team Member Role')
                    ->modalWidth('md'),
                
                Tables\Actions\Action::make('setDefault')
                    ->label('Set as Default')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn (User $record) => !$record->tenants->first()?->pivot->is_default)
                    ->requiresConfirmation()
                    ->action(function (User $record) use ($tenantId) {
                        // Remove default from all user's tenants
                        $record->tenants()->updateExistingPivot(
                            $record->tenants->pluck('id')->toArray(), 
                            ['is_default' => false]
                        );
                        
                        // Set current tenant as default
                        $record->tenants()->updateExistingPivot($tenantId, [
                            'is_default' => true,
                        ]);
                        
                        Notification::make()
                            ->title('Default tenant updated')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\DeleteAction::make()
                    ->label('Remove')
                    ->modalHeading('Remove Team Member')
                    ->modalDescription('Are you sure you want to remove this team member from your organization? They will no longer have access to your content.')
                    ->action(function (User $record) use ($tenantId) {
                        // Only detach from current tenant, don't delete user
                        $record->tenants()->detach($tenantId);
                        
                        Notification::make()
                            ->title('Team member removed from your organization')
                            ->success()
                            ->send();
                    })
                    ->successNotificationTitle('Team member removed'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assignRole')
                        ->label('Assign Role')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('role')
                                ->label('Role')
                                ->options([
                                    'tenant_admin' => 'Tenant Admin',
                                    'editor' => 'Editor',
                                    'author' => 'Author',
                                    'contributor' => 'Contributor',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (array $data, $records) use ($tenantId) {
                            foreach ($records as $record) {
                                $record->tenants()->updateExistingPivot($tenantId, [
                                    'role' => $data['role'],
                                ]);
                            }
                            
                            Notification::make()
                                ->title('Roles updated successfully')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('removeMembers')
                        ->label('Remove from Team')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) use ($tenantId) {
                            foreach ($records as $record) {
                                $record->tenants()->detach($tenantId);
                            }
                            
                            Notification::make()
                                ->title('Team members removed')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('No team members yet')
            ->emptyStateDescription('Start building your team by inviting members to collaborate.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->defaultSort('name');
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
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // Only tenant admins and editors can manage team members
        /** @var User|null $user */
        $user = Auth::user();
        $tenantId = session('tenant_id');
        
        if (!$tenantId || !$user) {
            return false;
        }
        
        $role = $user->getTenantRole($tenantId);
        return in_array($role, ['tenant_admin', 'editor']);
    }
}
