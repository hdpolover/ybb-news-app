<?php

namespace App\Filament\User\Resources\TeamMemberResource\Pages;

use App\Filament\User\Resources\TeamMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamMember extends EditRecord
{
    protected static string $resource = TeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resendInvitation')
                ->label('Resend Invitation')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->action(function () {
                    // TODO: Implement resend invitation email
                    \Filament\Notifications\Notification::make()
                        ->title('Invitation sent')
                        ->body('A new invitation email has been sent to the team member.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
            
            Actions\DeleteAction::make()
                ->label('Remove from Team')
                ->modalHeading('Remove Team Member')
                ->modalDescription('Are you sure you want to remove this team member from your organization?')
                ->action(function () {
                    $tenantId = session('current_tenant_id');
                    $this->record->tenants()->detach($tenantId);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Team member removed')
                        ->success()
                        ->send();
                    
                    return redirect()->to($this->getResource()::getUrl('index'));
                }),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Team Member';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $tenantId = session('current_tenant_id');
        $userTenant = $this->record->tenants()->where('tenant_id', $tenantId)->first();
        
        // Add pivot data to form
        $data['role'] = $userTenant?->pivot->role ?? 'author';
        $data['is_default'] = $userTenant?->pivot->is_default ?? false;
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store the role and is_default separately for pivot table
        $this->cachedRole = $data['role'] ?? null;
        $this->cachedIsDefault = $data['is_default'] ?? false;
        
        // Remove pivot fields from user data
        unset($data['role']);
        unset($data['is_default']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        $tenantId = session('current_tenant_id');
        
        // Update pivot table
        if ($this->cachedRole !== null) {
            $this->record->tenants()->updateExistingPivot($tenantId, [
                'role' => $this->cachedRole,
                'is_default' => $this->cachedIsDefault,
            ]);
            
            // Sync Spatie role based on pivot role
            $spatieRoleName = match($this->cachedRole) {
                'tenant_admin' => 'Tenant Admin',
                'editor' => 'Editor',
                'author' => 'Author',
                'contributor' => 'Contributor',
                default => 'Author',
            };
            
            // Remove old role and assign new one
            $allRoleNames = ['Tenant Admin', 'Editor', 'Author', 'Contributor'];
            $this->record->syncRoles([]); // Remove all roles first
            
            $role = \Spatie\Permission\Models\Role::where('name', $spatieRoleName)
                ->where('guard_name', 'web')
                ->first();
            
            if ($role) {
                // For Tenant Admin, ensure role has all permissions
                if ($spatieRoleName === 'Tenant Admin' && $role->permissions()->count() === 0) {
                    $allPermissions = \Spatie\Permission\Models\Permission::where('guard_name', 'web')->get();
                    $role->syncPermissions($allPermissions);
                }
                
                $this->record->assignRole($role);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private $cachedRole;
    private $cachedIsDefault;
}
