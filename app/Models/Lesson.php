<?php

namespace App\Models;

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

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function semester()
    {
        return $this->belongsTo(\App\Models\Semester::class);
    }

    public function directory()
    {
        return $this->belongsTo(\App\Models\Directory::class);
    }
}
