<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Tenant;

class TenantSwitcher extends Component
{
    public $currentTenant;
    public $availableTenants = [];
    public $isOpen = false;

    public function mount()
    {
        $this->loadTenants();
    }

    public function loadTenants()
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return;
        }

        $this->availableTenants = $user->tenants()
            ->orderBy('name')
            ->get()
            ->toArray();

        $currentTenantId = session('current_tenant_id');
        $this->currentTenant = $user->tenants()
            ->where('tenant_id', $currentTenantId)
            ->first();
    }

    public function switchTenant($tenantId)
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();
        
        if (!$user || !$user->hasAccessToTenant($tenantId)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have access to this tenant.'
            ]);
            return;
        }

        // Update session with new tenant
        session(['current_tenant_id' => $tenantId]);
        
        // Reload tenants to update current tenant
        $this->loadTenants();
        
        // Close dropdown
        $this->isOpen = false;
        
        // Redirect to refresh the page with new tenant context
        return redirect()->route('filament.user.pages.dashboard');
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        return view('livewire.tenant-switcher');
    }
}
