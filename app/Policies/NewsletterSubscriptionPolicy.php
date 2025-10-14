<?php

namespace App\Policies;

use App\Models\NewsletterSubscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsletterSubscriptionPolicy
{
    use HandlesAuthorization;

    // Tidak ada permission spesifik, hanya admin/owner yang boleh
    public function before(User $user, $ability): bool|null
    {
        if ($user->hasRole(['TenantOwner', 'Admin'])) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, NewsletterSubscription $newsletterSubscription): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, NewsletterSubscription $newsletterSubscription): bool
    {
        return false;
    }

    public function delete(User $user, NewsletterSubscription $newsletterSubscription): bool
    {
        return false;
    }
}
