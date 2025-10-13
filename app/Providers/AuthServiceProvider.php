<?php

namespace App\Providers;

use App\Models;
use App\Models\Admin; // <-- Tambahkan ini
use App\Policies;
use Illuminate\Contracts\Auth\Authenticatable; // <-- Tambahkan ini
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; // <-- Tambahkan ini

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        Models\Ad::class => Policies\AdPolicy::class,
        Models\EmailCampaign::class => Policies\EmailCampaignPolicy::class,
        Models\Media::class => Policies\MediaPolicy::class,
        Models\NewsletterSubscription::class => Policies\NewsletterSubscriptionPolicy::class,
        Models\Post::class => Policies\PostPolicy::class,
        Models\PtJob::class => Policies\PtJobPolicy::class,
        Models\PtProgram::class => Policies\PtProgramPolicy::class,
        Models\Redirect::class => Policies\RedirectPolicy::class,
        Models\SeoLanding::class => Policies\SeoLandingPolicy::class,
        Models\Term::class => Policies\TermPolicy::class,
        Models\User::class => Policies\UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Aturan Global: Berikan akses penuh ke semua Admin
        // Kode ini akan berjalan SEBELUM policy manapun dieksekusi.
        Gate::before(function (Authenticatable $user, string $ability) {
            // Jika user yang sedang login adalah instance dari model Admin,
            // langsung berikan akses `true` tanpa perlu cek policy lagi.
            if ($user instanceof Admin) {
                return true;
            }

            // Jika bukan Admin, lanjutkan ke pengecekan policy seperti biasa.
            return null;
        });
    }
}
