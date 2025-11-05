<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TermPost extends Pivot
{
    use HasFactory, HasUuids;

    protected $table = 'term_post';

    protected $fillable = [
        'post_id',
        'term_id',
        'tenant_id',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pivot) {
            if (empty($pivot->tenant_id) && !empty($pivot->post_id)) {
                $post = Post::find($pivot->post_id);
                if ($post) {
                    $pivot->tenant_id = $post->tenant_id;
                }
            }
        });
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
