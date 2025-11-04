<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ad extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'type',
        'placement',
        'content',
        'targeting',
        'settings',
        'is_active',
        'priority',
        'start_date',
        'end_date',
        'max_impressions',
        'max_clicks',
        'current_impressions',
        'current_clicks',
        'click_rate',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'content' => 'array',
        'targeting' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'placement' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'priority' => 'integer',
        'max_impressions' => 'integer',
        'max_clicks' => 'integer',
        'current_impressions' => 'integer',
        'current_clicks' => 'integer',
        'click_rate' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function impressions(): HasMany
    {
        return $this->hasMany(AdImpression::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(AdClick::class);
    }
}
