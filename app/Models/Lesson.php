<?php

namespace App\Models;

use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'legacy_id',
    'KM',
    'name',
    'category',
    'semester_id',
    'directory_id',
])]
class Lesson extends Model
{
    /** @use HasFactory<LessonFactory> */
    use HasFactory, SoftDeletes;

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function directory(): BelongsTo
    {
        return $this->belongsTo(Directory::class);
    }
}
