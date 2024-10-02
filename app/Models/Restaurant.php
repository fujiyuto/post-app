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
        'email',
        'tel_no',
        'price_min',
        'price_max'
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function user_store_restaurants(): HasMany
    {
        return $this->hasMany(UserStoreRestaurant::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'restaurant_genres', 'restaurant_id', 'genre_id');
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
            'genres'          => $this->genres->pluck('unique_name')->toArray()
        ];
    }
}
