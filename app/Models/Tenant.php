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
}
