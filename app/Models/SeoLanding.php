<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoLanding extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'seo_landings';

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'meta_description',
        'meta_title',
        'canonical_url',
        'content',
        'schema_markup',
        'target_keyword',
        'target_filters',
        'content_type',
        'items_per_page',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'schema_markup' => 'array',
        'target_filters' => 'array',
        'is_active' => 'boolean',
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
}
