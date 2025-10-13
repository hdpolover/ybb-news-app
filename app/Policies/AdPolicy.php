<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability): bool|null
    {
        if ($user->hasRole(['TenantOwner', 'Admin'])) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_ads');
    }

    public function view(User $user, Ad $ad): bool
    {
        return $user->can('view_ads');
    }

    public function create(User $user): bool
    {
        return $user->can('create_ads');
    }

    public function update(User $user, Ad $ad): bool
    {
        return $user->can('edit_ads');
    }

    public function delete(User $user, Ad $ad): bool
    {
        return $user->can('delete_ads');
    }
}
