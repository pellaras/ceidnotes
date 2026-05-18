<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'legacy_id',
        'user_id',
        'likeable_id',
        'likeable_type',
        'value',
        'updated_at',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function likeable()
    {
        return $this->morphTo();
    }

    public function scopeWithoutTimestamps()
    {
        $this->timestamps = false;
        return $this;
    }
}
