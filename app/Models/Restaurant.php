<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Restaurant extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'restaurant_name',
        'zip_cd',
        'address',
        'address_detail',
        'email',
        'tel_no',
        'price_min',
        'price_max',
        'seating_duration',
        'is_reservable',
        'capacity',
        'open_time',
        'close_time',
        'lunch_open_time',
        'lunch_close_time',
        'dinner_open_time',
        'dinner_close_time'
    ];

    // 投稿テーブルとのリレーション
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // ユーザー保存店テーブルとのリレーション
    public function user_store_restaurants(): HasMany
    {
        return $this->hasMany(UserStoreRestaurant::class);
    }

    // ジャンルテーブルとのリレーション
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'restaurant_genres', 'restaurant_id', 'genre_id');
    }

    // 店画像テーブルとのリレーション
    public function restaurant_images(): HasMany
    {
        return $this->hasMany(RestaurantImage::class);
    }

    // 予約テーブルとのリレーション
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // 店休日テーブルとのリレーション
    public function restaurant_holidays(): HasMany
    {
        return $this->hasMany(RestaurantHoliday::class);
    }

    // 予約状況テーブルとのリレーション
    public function restaurant_reservation_status(): HasMany
    {
        return $this->hasMany(RestaurantReservationStatus::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id'              => $this->id,
            'restaurant_name' => $this->restaurant_name,
            'address'         => $this->address,
            'price_min'       => $this->price_min,
            'price_max'       => $this->price_max,
            'post_num'        => $this->post_num,
            'point_avg'       => $this->point_avg,
            'genres'          => $this->genres->pluck('unique_name')->toArray(),
            'updated_at'      => $this->updated_at
        ];
    }
}
