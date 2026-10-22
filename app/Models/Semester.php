<?php

namespace App\Models;

use Database\Factories\SemesterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'legacy_id',
    'name',
])]
class Semester extends Model
{
    /** @use HasFactory<SemesterFactory> */
    use HasFactory, SoftDeletes;

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}
