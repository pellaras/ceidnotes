<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Directory extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'legacy_id',
		'directory_id',
        'name',
        'path',
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

    public function directories()
    {
        return $this->hasMany('App\Directory');
    }

    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function calculatePath()
    {
        $path = "";
        $currentDirectory = $this;

        do {
            if ($path != "") {
                $path = "/" . $path;
            }
            $path = prepair_path($currentDirectory->name) . $path;
            $currentDirectory = $currentDirectory->directory;
        } while ($currentDirectory != null);

        $this->path = $path;

        return $this;
    }

	public function scopeWithoutTimestamps()
    {
        $this->timestamps = false;
        return $this;
	}
}
