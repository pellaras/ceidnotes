<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
		'deleted_at',
    ];

	public function likes()
    {
        return $this->hasMany('App\Like');
    }

	public function reports()
    {
        return $this->hasMany('App\Report');
    }

	public function edits()
    {
        return $this->hasMany('App\Edit');
    }

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

    public static function getDirectoryData($username)
    {
        return Cache::remember('directory.user.' . $username, $seconds = 60 * 60, function () use ($username) {
            $client = new Client([
                'form_params' => [
                    'attribute' => 'uid',
                    'criterion' => '=',
                    'keyword' => $username,
                    'dn' => 'cn=users,cn=accounts,dc=ceid,dc=upatras,dc=gr',
                    'search' => 'search',
                ],
                'verify' => false
            ]);

            $response = $client->post('https://directory.ceid.upatras.gr/');

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $regex = '/<td class="n">\s*' .
                '<span class="given-name">(.*)<\/span>\s*' .
                '<span class="family-name">(.*)<\/span>\s*' .
                '<\/td>\s*' .
                '<td class="fn">(.*)<\/td>\s*' .
                '<td class="org">CEID<\/td>\s*' .
                '<!-- <td class="title"><\/td> -->\s*' .
                '<td class="title">(.*)<\/td>\s*' .
                '<td class="tel">([0-9]+)\/([0-9]+)<\/td>\s*' .
                '<td class="email"><a href="mailto:' . $username . '@ceid.upatras.gr">' . $username . '@ceid.upatras.gr<\/a><\/td>/';

            if (! preg_match($regex, $response->getBody()->getContents(), $parsed_data)) {
                return null;
            }

            return [
                'name' => $parsed_data[3],
                'AM' => $parsed_data[5],
                'registration_year' => $parsed_data[6],
            ];
        });
    }
}
