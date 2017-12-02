<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'legacy_id',
        'code',
        'name',
        'total_files',
    ];

    protected $dates = [
		'deleted_at',
    ];

    public function files()
    {
        return $this->belongsToMany('App\File')->withTimestamps();
    }
}
