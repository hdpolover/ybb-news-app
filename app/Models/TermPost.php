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
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
