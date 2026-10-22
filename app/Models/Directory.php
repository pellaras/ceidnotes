<?php

namespace App\Models;

use Database\Factories\DirectoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'legacy_id',
    'directory_id',
    'name',
    'path',
    'user_id',
    'deleted_by_user_id',
    'deleted_at',
    'updated_at',
    'created_at',
])]
class Directory extends Model
{
    /** @use HasFactory<DirectoryFactory> */
    use HasFactory, SoftDeletes;

    public function directory(): BelongsTo
    {
        return $this->belongsTo(Directory::class);
    }

    public function directories(): HasMany
    {
        return $this->hasMany(Directory::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function edits(): MorphMany
    {
        return $this->morphMany(Edit::class, 'editable');
    }

    public function calculatePath(): static
    {
        $path = '';
        $currentDirectory = $this;

        do {
            if ($path != '') {
                $path = '/'.$path;
            }
            $path = prepair_path($currentDirectory->name).$path;
            $currentDirectory = $currentDirectory->directory;
        } while ($currentDirectory != null);

        $this->path = $path;

        return $this;
    }
}
