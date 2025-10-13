<?php

namespace App\Policies;

use App\Models\SeoLanding;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeoLandingPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability): bool|null
    {
        if ($user->hasRole(['TenantOwner', 'Admin'])) {
            return true;
        }
        return null;
    }

    // Permission 'manage_seo' meng-cover semua aksi CRUD
    public function viewAny(User $user): bool
    {
        return $user->can('manage_seo');
    }

    public function view(User $user, SeoLanding $seoLanding): bool
    {
        return $user->can('manage_seo');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_seo');
    }

    public function update(User $user, SeoLanding $seoLanding): bool
    {
        return $user->can('manage_seo');
    }

    public function delete(User $user, SeoLanding $seoLanding): bool
    {
        return $user->can('manage_seo');
    }
}
