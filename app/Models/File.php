<?php

namespace App\Models;

use Database\Factories\FileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'legacy_id',
    'directory_id',
    'name',
    'type',
    'path',
    'md5',
    'user_id',
    'deleted_by_user_id',
    'deleted_at',
    'updated_at',
    'created_at',
    'is_owned',
    'comment',
    'size',
    'total_views',
    'total_downloads',
    'total_overall',
    'votes_up',
    'votes_down',
])]
class File extends Model
{
    /** @use HasFactory<FileFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return BelongsTo<Directory, $this>
     */
    public function directory(): BelongsTo
    {
        return $this->belongsTo(Directory::class);
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class)->withTimestamps();
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
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
        $this->path = $this->directory()->withTrashed()->first()->path.'/'.prepair_path($this->name, true);

        return $this;
    }
}
