<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailCampaign extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'email_campaigns';

    protected $fillable = [
        'tenant_id',
        'name',
        'subject',
        'preview_text',
        'content',
        'type',
        'status',
        'recipient_criteria',
        'scheduled_at',
        'sent_at',
        'template',
        'settings',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'recipient_criteria' => 'array',
        'settings' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
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
