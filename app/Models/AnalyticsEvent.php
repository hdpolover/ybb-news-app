<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'analytics_events';

    // Nonaktifkan updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'event_type',
        'event_category',
        'event_action',
        'event_label',
        'event_value',
        'page_url',
        'page_title',
        'content_id',
        'content_type',
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'referrer',
        'utm_params',
        'device_type',
        'browser',
        'os',
        'country',
        'city',
        'custom_data',
    ];

    protected $casts = [
        'event_value' => 'decimal:2',
        'utm_params' => 'array',
        'custom_data' => 'array',
        'created_at' => 'datetime', // 'created_at' di-cast agar konsisten
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
