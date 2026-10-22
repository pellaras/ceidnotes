<?php

namespace App\Models;

use Database\Factories\LikeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'legacy_id',
    'user_id',
    'likeable_id',
    'likeable_type',
    'value',
    'updated_at',
    'created_at',
])]
class Like extends Model
{
    /** @use HasFactory<LikeFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}
