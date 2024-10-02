<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'title',
        'content',
        'visited_at',
        'period_of_time',
        'points',
        'price_min',
        'price_max',
        'image_url1',
        'image_url2',
        'image_url3',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'content'    => $this->content,
            'visited_at' => $this->visited_at,
            'points'     => $this->points,
            'price_min'  => $this->price_min,
            'price_max'  => $this->price_max
        ];
    }

    protected function makeAllSearchableUsing(Builder $query)
    {
        return $query->with(['user', 'restaurant']);
    }
}
