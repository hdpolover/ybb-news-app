<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Scopes\TenantScope;

class Post extends Model
{
    use HasFactory, HasUuids;

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    protected $fillable = [
        'tenant_id',
        'kind',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'cover_image_url',
        'meta_title',
        'meta_description',
        'og_image_url',
        'canonical_url',
        'published_at',
        'scheduled_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(Term::class, 'term_post')
            ->using(TermPost::class)
            ->withPivot('tenant_id');
    }

    public function program(): HasOne
    {
        return $this->hasOne(PtProgram::class);
    }

    public function job(): HasOne
    {
        return $this->hasOne(PtJob::class);
    }
}
