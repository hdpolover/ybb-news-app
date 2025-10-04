<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdClick extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ad_clicks';

    // Nonaktifkan updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'ad_id',
        'impression_id',
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'click_url',
        'page_url',
        'referrer',
        'device_type',
        'browser',
        'os',
        'country',
        'city',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    public function impression(): BelongsTo
    {
        return $this->belongsTo(AdImpression::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
