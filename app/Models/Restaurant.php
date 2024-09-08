<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

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
}
