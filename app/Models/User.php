<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, HasUuids, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar_url',
        'bio',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return '/storage/' . $this->avatar_url;
    }

    // Tenant relationships
    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'user_tenants', 'user_id', 'tenant_id')
            ->withPivot('role', 'is_default')
            ->withTimestamps();
    }

    public function defaultTenant()
    {
        return $this->belongsToMany(Tenant::class, 'user_tenants', 'user_id', 'tenant_id')
            ->wherePivot('is_default', true)
            ->withPivot('role', 'is_default')
            ->withTimestamps()
            ->first();
    }

    public function hasAccessToTenant($tenantId): bool
    {
        return $this->tenants()->where('tenant_id', $tenantId)->exists();
    }

    public function isTenantAdmin($tenantId = null): bool
    {
        $query = $this->tenants()->wherePivot('role', 'tenant_admin');
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        return $query->exists();
    }

    public function getTenantRole($tenantId): ?string
    {
        $tenant = $this->tenants()->where('tenant_id', $tenantId)->first();
        return $tenant?->pivot->role;
    }
}
