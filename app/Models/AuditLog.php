<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'audit_logs';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get a human-readable description of the event
     */
    public function getDescriptionAttribute(): string
    {
        $userName = $this->user?->name ?? 'System';
        $resourceType = class_basename($this->auditable_type);
        
        return match($this->event) {
            'created' => "{$userName} created a new {$resourceType}",
            'updated' => "{$userName} updated {$resourceType}",
            'deleted' => "{$userName} deleted {$resourceType}",
            'viewed' => "{$userName} viewed {$resourceType}",
            'restored' => "{$userName} restored {$resourceType}",
            'published' => "{$userName} published {$resourceType}",
            'unpublished' => "{$userName} unpublished {$resourceType}",
            'approved' => "{$userName} approved {$resourceType}",
            'rejected' => "{$userName} rejected {$resourceType}",
            default => "{$userName} performed {$this->event} on {$resourceType}",
        };
    }

    /**
     * Get changed fields for display
     */
    public function getChangedFieldsAttribute(): array
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        $changed = [];
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changed[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changed;
    }

    /**
     * Log an audit event
     */
    public static function log(
        string $event,
        Model $auditable,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'tenant_id' => session('tenant_id'),
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => get_class($auditable),
            'auditable_id' => $auditable->getKey(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
        ]);
    }
}
