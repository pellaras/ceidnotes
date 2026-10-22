<?php

namespace App\Models;

use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'legacy_id',
    'user_id',
    'email',
    'reportable_id',
    'reportable_type',
    'reason',
    'deleted_at',
    'updated_at',
    'created_at',
])]
class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }
}
