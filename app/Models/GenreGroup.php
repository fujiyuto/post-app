<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GenreGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_name',
        'group_name'
    ];

    public function genres(): HasMany
    {
        return $this->hasMany(Genre::class);
    }
}
