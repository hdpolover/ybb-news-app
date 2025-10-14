<?php

namespace App\Policies;

use App\Models\Redirect;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RedirectPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability): bool|null
    {
        if ($user->hasRole(['TenantOwner', 'Admin'])) {
            return true;
        }
        return null;
    }

    // Menggunakan permission 'manage_seo' juga untuk redirect
    public function viewAny(User $user): bool
    {
        return $user->can('manage_seo');
    }

    public function view(User $user, Redirect $redirect): bool
    {
        return $user->can('manage_seo');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_seo');
    }

    public function update(User $user, Redirect $redirect): bool
    {
        return $user->can('manage_seo');
    }

    public function delete(User $user, Redirect $redirect): bool
    {
        return $user->can('manage_seo');
    }
}
