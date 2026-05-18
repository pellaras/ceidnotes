<?php

namespace App\Models;

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

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function files()
    {
        return $this->belongsToMany(\App\Models\File::class)->withTimestamps();
    }
}
