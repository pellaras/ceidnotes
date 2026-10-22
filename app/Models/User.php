<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
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
])]
#[Hidden(['password', 'password_old', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function edits(): HasMany
    {
        return $this->hasMany(Edit::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class);
    }

    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class);
    }
}
