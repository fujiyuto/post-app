<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantReservationStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'reserve_date',
        'reserve_status_info',
        'is_reservable'
    ];

    // 予約可能か
    const IS_RESERVABLE     = true;
    const IS_NOT_RESERVABLE = false;

    // 店テーブルとのリレーション
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
