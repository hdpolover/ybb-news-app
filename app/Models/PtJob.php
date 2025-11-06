<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PtJob extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pt_job';

    protected $fillable = [
        'post_id',
        'company_name',
        'employment_type',
        'workplace_type',
        'title_override',
        'location_city',
        'country_code',
        'min_salary',
        'max_salary',
        'salary_currency',
        'salary_period',
        'experience_level',
        'responsibilities',
        'requirements',
        'benefits',
        'deadline_at',
        'apply_url',
        'extra',
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'benefits' => 'array',
        'deadline_at' => 'datetime',
        'extra' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    // Access tenant through post relationship
    public function getTenantAttribute()
    {
        return $this->post->tenant;
    }
}
