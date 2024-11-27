<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token'
    ];

    public const PASSWORD_TOKEN_VALID = 1;

    public const PASSWORD_TOKEN_INVALID = 2;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
