<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'legacy_id',
        'name',
    ];

    protected $dates = [
		'deleted_at',
    ];

    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }
}
