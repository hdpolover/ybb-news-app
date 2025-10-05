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
        'estimated_recipients',
        'actual_recipients',
        'scheduled_at',
        'sent_at',
        'emails_sent',
        'emails_delivered',
        'emails_opened',
        'emails_clicked',
        'emails_bounced',
        'emails_unsubscribed',
        'open_rate',
        'click_rate',
        'bounce_rate',
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
        'open_rate' => 'decimal:2',
        'click_rate' => 'decimal:2',
        'bounce_rate' => 'decimal:2',
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
