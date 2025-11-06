<?php

namespace App\Policies;

use App\Models\Term;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TermPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability): bool|null
    {
        // Platform admins and tenant owners have full access
        if ($user->hasRole(['TenantOwner', 'Admin'])) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        // Tenant Admin, Editor, and Author can view terms
        return $user->hasRole(['Tenant Admin', 'Editor', 'Author']);
    }

    public function view(User $user, Term $term): bool
    {
        // Anyone who can view any can view individual terms
        return $user->hasRole(['Tenant Admin', 'Editor', 'Author']);
    }

    public function create(User $user): bool
    {
        // Only Tenant Admin and Editor can create terms
        return $user->hasRole(['Tenant Admin', 'Editor']);
    }

    public function update(User $user, Term $term): bool
    {
        // Only Tenant Admin and Editor can update terms
        return $user->hasRole(['Tenant Admin', 'Editor']);
    }

    public function delete(User $user, Term $term): bool
    {
        // Only Tenant Admin and Editor can delete terms
        return $user->hasRole(['Tenant Admin', 'Editor']);
    }
}
