<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserved_by',
        'restaurant_id',
        'reserve_date',
        'reserve_time',
        'num_of_people',
        'status',
        'notes',
        'updated_by'
    ];

    const STATUS_PENDING = 'PENDING';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_COMPLETED = 'COMPLETED';

    // ユーザーテーブルとのリレーション
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reserved_by');
    }

    // 店テーブルとのリレーション
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
