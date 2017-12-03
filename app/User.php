<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'password_old',
        'remember_token',
    ];

    protected $dates = [
		'deleted_at',
    ];

	public function phones()
    {
        return $this->hasMany('App\Phone');
    }

	public function phone()
    {
        return $this->belongsTo('App\Phone');
    }

	public function scopeWithoutTimestamps()
    {
        $this->timestamps = false;
        return $this;
	}
}
