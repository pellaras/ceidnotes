<?php

namespace App\Models;

use Database\Factories\EditFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'legacy_id',
    'user_id',
    'editable_id',
    'editable_type',
    'modified_data',
    'updated_at',
    'created_at',
])]
class Edit extends Model
{
    /** @use HasFactory<EditFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'modified_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function editable(): MorphTo
    {
        return $this->morphTo();
    }
}
