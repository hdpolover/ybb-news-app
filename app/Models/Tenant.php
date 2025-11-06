<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\HasName;

class Tenant extends Model implements HasName
{
    use HasFactory, HasUuids;

    public function getFilamentName(): string
    {
        return $this->name;
    }

    protected $fillable = [
        'name',
        'domain',
        'logo_url',
        'description',
        'primary_color',
        'secondary_color',
        'accent_color',
        'meta_title',
        'meta_description',
        'og_image_url',
        'favicon_url',
        'google_analytics_id',
        'google_adsense_id',
        'google_tag_manager_id',
        'email_from_name',
        'email_from_address',
        'gdpr_enabled',
        'ccpa_enabled',
        'privacy_policy_url',
        'terms_of_service_url',
        'enabled_features',
        'settings',
        'status',
    ];

    protected $casts = [
        'enabled_features' => 'array',
        'settings' => 'array',
        'gdpr_enabled' => 'boolean',
        'ccpa_enabled' => 'boolean',
    ];

    // User relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_tenants', 'tenant_id', 'user_id')
            ->withPivot('role', 'is_default')
            ->withTimestamps();
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_tenants', 'tenant_id', 'admin_id')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function tenantAdmins()
    {
        return $this->belongsToMany(User::class, 'user_tenants', 'tenant_id', 'user_id')
            ->wherePivot('role', 'tenant_admin')
            ->withPivot('role', 'is_default')
            ->withTimestamps();
    }

    // Content relationships
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function emailCampaigns()
    {
        return $this->hasMany(EmailCampaign::class);
    }

    public function newsletterSubscriptions()
    {
        return $this->hasMany(NewsletterSubscription::class);
    }

    public function seoLandings()
    {
        return $this->hasMany(SeoLanding::class);
    }

    public function redirects()
    {
        return $this->hasMany(Redirect::class);
    }
}
