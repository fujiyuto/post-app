<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImageCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_cd',
        'name'
    ];

    public function restaurant_images(): HasMany
    {
        return $this->hasMany(RestaurantImage::class);
    }
}
