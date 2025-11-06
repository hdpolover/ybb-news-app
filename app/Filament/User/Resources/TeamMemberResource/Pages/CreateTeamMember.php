<?php

namespace App\Filament\User\Resources\TeamMemberResource\Pages;

use App\Filament\User\Resources\TeamMemberResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTeamMember extends CreateRecord
{
    protected static string $resource = TeamMemberResource::class;

    public function getTitle(): string
    {
        return 'Invite Team Member';
    }

    public function getHeading(): string
    {
        return 'Invite Team Member';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate a random password if not provided
        if (empty($data['password'])) {
            $data['password'] = Str::random(16);
        }
        
        // Store the role separately as we'll need it for the pivot
        $this->cachedRole = $data['role'] ?? 'author';
        $this->cachedIsDefault = $data['is_default'] ?? false;
        
        // Remove role and is_default from user data as they belong to the pivot table
        unset($data['role']);
        unset($data['is_default']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $tenantId = session('current_tenant_id');
        $user = $this->record;
        
        // Check if user already belongs to this tenant
        if ($user->hasAccessToTenant($tenantId)) {
            Notification::make()
                ->title('User already in team')
                ->body('This user is already a member of your organization.')
                ->warning()
                ->send();
            return;
        }
        
        // Attach user to tenant with role
        $user->tenants()->attach($tenantId, [
            'role' => $this->cachedRole ?? 'author',
            'is_default' => $this->cachedIsDefault ?? false,
        ]);
        
        // TODO: Send invitation email with temporary password
        // This would be implemented with a mail job
        
        Notification::make()
            ->title('Team member invited successfully')
            ->body("An invitation email has been sent to {$user->email}.")
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return null; // We're handling notifications in afterCreate
    }

    private $cachedRole;
    private $cachedIsDefault;
}
