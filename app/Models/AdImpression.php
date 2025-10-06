<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdImpression extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ad_impressions';

    // Tabel ini hanya punya created_at, jadi kita nonaktifkan updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'ad_id',
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
