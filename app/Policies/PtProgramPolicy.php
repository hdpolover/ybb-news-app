<?php

namespace App\Policies;

use App\Models\PtProgram;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PtProgramPolicy
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
        return $user->can('view_programs');
    }

    public function view(User $user, PtProgram $ptProgram): bool
    {
        return $user->can('view_programs');
    }

    public function create(User $user): bool
    {
        return $user->can('create_programs');
    }

    public function update(User $user, PtProgram $ptProgram): bool
    {
        // Author hanya boleh mengedit program yang terhubung dengan post miliknya
        if ($user->hasRole('Author')) {
            return $user->can('edit_programs') && optional($ptProgram->post)->created_by === $user->id;
        }
        return $user->can('edit_programs');
    }

    public function delete(User $user, PtProgram $ptProgram): bool
    {
        return $user->can('delete_programs');
    }
}
