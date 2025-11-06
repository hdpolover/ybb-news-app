<?php

namespace App\Filament\User\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class SwitchTenant extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static string $view = 'filament.user.pages.switch-tenant';
    
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Switch Tenant';

    public function switchTenant(string $tenantId): void
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        if ($user && $user->hasAccessToTenant($tenantId)) {
            session(['current_tenant_id' => $tenantId]);
            
            $tenant = $user->tenants()->find($tenantId);
            
            Notification::make()
                ->success()
                ->title('Tenant Switched')
                ->body("You are now working in: {$tenant->name}")
                ->send();

            $this->redirect(route('filament.app.pages.dashboard'));
        } else {
            Notification::make()
                ->danger()
                ->title('Access Denied')
                ->body('You do not have access to this tenant')
                ->send();
        }
    }

    public function getTenants()
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();
        return $user->tenants()->get();
    }

    public function getCurrentTenantId(): ?string
    {
        return session('current_tenant_id');
    }
}
