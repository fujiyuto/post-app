<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Genre extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'unique_name',
        'genre_name'
    ];

    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'restaurant_genres', 'genre_id', 'restaurant_id');
    }

    public function toSearchableArray(): array
    {
        return [
            'id'         => $this->id,
            'unique_name'  => $this->unique_cd,
            'genre_name' => $this->genre_name
        ];
    }
}
