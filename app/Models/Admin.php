<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class Admin extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'settings' => 'array',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    // Tenant relationships (for future use)
    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'admin_tenants', 'admin_id', 'tenant_id')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function hasAccessToTenant($tenantId): bool
    {
        // Platform superadmins have access to all tenants
        if ($this->role === 'superadmin') {
            return true;
        }
        
        // Check if admin is assigned to specific tenant
        return $this->tenants()->where('tenant_id', $tenantId)->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }
}
