<?php

namespace App\Models;

use Database\Factories\LabelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'legacy_id',
    'code',
    'name',
    'total_files',
])]
class Label extends Model
{
    /** @use HasFactory<LabelFactory> */
    use HasFactory, SoftDeletes;

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class)->withTimestamps();
    }
}
