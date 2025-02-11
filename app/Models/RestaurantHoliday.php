<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'holiday_type',
        'day_of_week',
        'specific_date',
        'restaurant_id'
    ];

    // 店テーブルとのリレーション
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
