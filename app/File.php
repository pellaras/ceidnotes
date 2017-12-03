<?php

namespace App;

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

    protected $dates = [
		'deleted_at',
    ];

    public function directory()
    {
        return $this->belongsTo('App\Directory');
    }

    public function labels()
    {
        return $this->belongsToMany('App\Label')->withTimestamps();
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
