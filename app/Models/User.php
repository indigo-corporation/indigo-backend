<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Nnjeim\World\Models\City;

/**
 * @property int id
 * @property string name
 * @property string email

 * @mixin Eloquent
 * @mixin Builder
 */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function comments (): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function city() :BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function country() :HasOneThrough
    {
        return $this->hasOneThrough(
            Country::class,
            City::class,
            'id',
            'id',
            'city_id',
            'country_id'
        );
    }

    public function favorite_films(): HasMany
    {
        return $this->hasMany(FavoriteFilms::class);
    }
}
