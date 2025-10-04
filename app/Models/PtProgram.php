<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PtProgram extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pt_program';

    protected $fillable = [
        'tenant_id',
        'post_id',
        'program_type',
        'organizer_name',
        'location_text',
        'country_code',
        'deadline_at',
        'is_rolling',
        'funding_type',
        'stipend_amount',
        'fee_amount',
        'program_length_text',
        'eligibility_text',
        'apply_url',
        'extra',
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
        'is_rolling' => 'boolean',
        'stipend_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'extra' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
