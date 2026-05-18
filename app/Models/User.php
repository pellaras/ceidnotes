<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'password_old',
        'legacy_id',
        'username',
        'AM',
        'registration_year',
        'send_results_by_email',
        'phone_id',
        'phone_notifications_start',
        'phone_notifications_end',
        'is_admin',
        'deleted_at',
        'updated_at',
        'created_at',
    ];

    protected $hidden = [
        'password',
        'password_old',
        'remember_token',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function likes()
    {
        return $this->hasMany(\App\Models\Like::class);
    }

    public function reports()
    {
        return $this->hasMany(\App\Models\Report::class);
    }

    public function edits()
    {
        return $this->hasMany(\App\Models\Edit::class);
    }

    public function phones()
    {
        return $this->hasMany(\App\Models\Phone::class);
    }

    public function phone()
    {
        return $this->belongsTo(\App\Models\Phone::class);
    }

    public function scopeWithoutTimestamps()
    {
        $this->timestamps = false;
        return $this;
    }
}
