<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function directory()
    {
        return $this->belongsTo(\App\Models\Directory::class);
    }

    public function labels()
    {
        return $this->belongsToMany(\App\Models\Label::class)->withTimestamps();
    }

    public function likes()
    {
        return $this->morphMany(\App\Models\Like::class, 'likeable');
    }

    public function reports()
    {
        return $this->morphMany(\App\Models\Report::class, 'reportable');
    }

    public function edits()
    {
        return $this->morphMany(\App\Models\Edit::class, 'editable');
    }

    public function calculatePath()
    {
        $this->path = $this->directory()->withTrashed()->first()->path . "/" . prepair_path($this->name, true);

        return $this;
    }

    public function scopeWithoutTimestamps()
    {
        $this->timestamps = false;
        return $this;
    }
}
