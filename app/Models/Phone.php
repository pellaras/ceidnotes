<?php

namespace App\Models;

use Database\Factories\PhoneFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'legacy_id',
    'user_id',
    'phone_number',
    'verification_code',
    'verified',
    'deleted_at',
    'updated_at',
    'created_at',
])]
class Phone extends Model
{
    /** @use HasFactory<PhoneFactory> */
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
