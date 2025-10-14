<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
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
        return $user->can('view_posts');
    }

    public function view(User $user, Post $post): bool
    {
        return $user->can('view_posts');
    }

    public function create(User $user): bool
    {
        return $user->can('create_posts');
    }

    public function update(User $user, Post $post): bool
    {
        // Author hanya boleh mengedit post miliknya sendiri
        if ($user->hasRole('Author')) {
            return $user->can('edit_posts') && $post->created_by === $user->id;
        }
        return $user->can('edit_posts');
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can('delete_posts');
    }
}
