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
    ];

    protected $dates = [
		'deleted_at',
    ];

    public function directory()
    {
        return $this->belongsTo('App\Directory');
    }

    public function calculatePath()
    {
        $this->path = $this->directory()->withTrashed()->first()->path . "/" . $this->name;

        return $this;
    }

	public function scopeWithoutTimestamps()
    {
        $this->timestamps = false;
        return $this;
	}
}
