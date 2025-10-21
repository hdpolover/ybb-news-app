<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TermPost extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'term_id',
    ];

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'term_post');
    }
}
