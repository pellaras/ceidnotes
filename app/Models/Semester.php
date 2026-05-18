<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'legacy_id',
        'name',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function lessons()
    {
        return $this->hasMany(\App\Models\Lesson::class);
    }
}
