<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'zip_cd',
        'address',
        'email',
        'tel_no',
        'price_min',
        'price_max'
    ];

    
}
