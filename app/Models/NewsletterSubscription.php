<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterSubscription extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'newsletter_subscriptions';

    protected $fillable = [
        'tenant_id',
        'email',
        'name',
        'preferences',
        'status',
        'frequency',
        'verification_token',
        'verified_at',
        'unsubscribe_token',
        'tags',
        'source',
        'ip_address',
        'user_agent',
        'last_sent_at',
        'emails_sent',
        'emails_opened',
        'links_clicked',
    ];

    protected $casts = [
        'preferences' => 'array',
        'tags' => 'array',
        'verified_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'emails_sent' => 'integer',
        'emails_opened' => 'integer',
        'links_clicked' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
