<?php

namespace App\Policies;

use App\Models\PtJob;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PtJobPolicy
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
        return $user->can('view_jobs');
    }

    public function view(User $user, PtJob $ptJob): bool
    {
        return $user->can('view_jobs');
    }

    public function create(User $user): bool
    {
        return $user->can('create_jobs');
    }

    public function update(User $user, PtJob $ptJob): bool
    {
        // Author hanya boleh mengedit job yang terhubung dengan post miliknya
        if ($user->hasRole('Author')) {
            return $user->can('edit_jobs') && optional($ptJob->post)->created_by === $user->id;
        }
        return $user->can('edit_jobs');
    }

    public function delete(User $user, PtJob $ptJob): bool
    {
        return $user->can('delete_jobs');
    }
}
