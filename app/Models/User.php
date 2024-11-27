<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'email',
        'tel_no',
        'birthday',
        'password',
        'gender',
        'user_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at'
    ];

    public const USER_GENDER_MEN   = 1;
    public const USER_GENDER_WOMEN = 2;
    public const USER_GENDER_MAP = [
        self::USER_GENDER_MEN   => '男性',
        self::USER_GENDER_WOMEN => '女性'
    ];

    public const USER_TYPE_CUSTOMER = 1;
    public const USER_TYPE_OWNER    = 2;
    const USER_TYPE_MAP = [
        self::USER_TYPE_CUSTOMER => '顧客',
        self::USER_TYPE_OWNER    => '店主'
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function user_store_restaurants(): HasMany
    {
        return $this->hasMany(UserStoreRestaurant::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id'            => $this->id,
            'user_name'     => $this->user_name,
            'name_sei'      => $this->name_sei,
            'name_mei'      => $this->name_mei,
            'name_sei_kana' => $this->name_sei_kana ?? '',
            'name_mei_kana' => $this->name_mei_kana ?? '',
        ];
    }
}
