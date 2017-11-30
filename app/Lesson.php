<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'legacy_id',
        'KM',
        'name',
        'category',
        'semester_id',
		'directory_id',
    ];

    protected $dates = [
		'deleted_at',
    ];

    public function semester()
    {
        return $this->belongsTo('App\Semester');
    }

    public function directory()
    {
        return $this->belongsTo('App\Directory');
    }
}
