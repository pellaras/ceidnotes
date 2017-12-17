<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'legacy_id',
        'user_id',
        'email',
        'reportable_id',
        'reportable_type',
        'reason',
		'deleted_at',
        'updated_at',
        'created_at',
    ];

    protected $dates = [
		'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function reportable()
    {
        return $this->morphTo();
    }

	public function scopeWithoutTimestamps()
    {
        $this->timestamps = false;
        return $this;
	}
}
