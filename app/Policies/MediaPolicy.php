<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
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
        return $user->can('view_media');
    }

    public function view(User $user, Media $media): bool
    {
        return $user->can('view_media');
    }

    public function create(User $user): bool
    {
        return $user->can('upload_media');
    }

    public function update(User $user, Media $media): bool
    {
        // Mengasumsikan 'upload_media' juga berarti bisa mengedit info media
        // Author hanya boleh mengedit media miliknya sendiri
        if ($user->hasRole('Author')) {
            return $user->can('upload_media') && $media->uploaded_by === $user->id;
        }
        return $user->can('upload_media');
    }

    public function delete(User $user, Media $media): bool
    {
        // Author hanya boleh menghapus media miliknya sendiri
        if ($user->hasRole('Author')) {
            return $user->can('delete_media') && $media->uploaded_by === $user->id;
        }
        return $user->can('delete_media');
    }
}
