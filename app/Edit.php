<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Edit extends Model
{
    protected $fillable = [
        'legacy_id',
        'user_id',
        'editable_id',
        'editable_type',
        'modified_data',
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'modified_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function editable()
    {
        return $this->morphTo();
    }

	public function scopeWithoutTimestamps()
    {
        $this->timestamps = false;
        return $this;
	}
}
