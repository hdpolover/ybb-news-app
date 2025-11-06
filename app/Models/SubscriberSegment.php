<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriberSegment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'subscriber_segments';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'criteria',
        'type',
        'subscriber_count',
    ];

    protected $casts = [
        'criteria' => 'array',
        'subscriber_count' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(NewsletterSubscription::class, 'segment_subscriber', 'segment_id', 'subscriber_id')
            ->withTimestamps();
    }

    /**
     * Update subscriber count for this segment
     */
    public function updateSubscriberCount(): void
    {
        $this->update(['subscriber_count' => $this->subscribers()->count()]);
    }

    /**
     * Apply dynamic criteria to get matching subscribers
     */
    public function getDynamicSubscribers()
    {
        if ($this->type !== 'dynamic' || empty($this->criteria)) {
            return collect();
        }

        $query = NewsletterSubscription::where('tenant_id', $this->tenant_id);

        // Apply dynamic criteria
        foreach ($this->criteria as $field => $value) {
            if ($field === 'status') {
                $query->where('status', $value);
            } elseif ($field === 'frequency') {
                $query->where('frequency', $value);
            } elseif ($field === 'created_after') {
                $query->where('created_at', '>=', $value);
            } elseif ($field === 'created_before') {
                $query->where('created_at', '<=', $value);
            }
        }

        return $query->get();
    }
}
