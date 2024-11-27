<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Scout\Searchable;

class Tweet extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'message'
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
            'id'              => $this->id,
            'message'         => $this->message,
            'user_id'         => $this->user_id,
            'user_name'       => $this->user->user_name,
            'restaurant_id'   => $this->restaurant_id,
            'restaurant_name' => $this->restaurant->restaurant_name
        ];
    }
}
