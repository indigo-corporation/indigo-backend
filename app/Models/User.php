<?php

namespace App\Models;

use App\Models\Film\Film;
use App\Services\ImageService;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Nnjeim\World\Models\City;

/**
 * @property int id
 * @property string name
 * @property string email
 *
 * @mixin Eloquent
 * @mixin Builder
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_name',
        'email',
        'password',
        'birth_date',
        'about',
        'city_id',
        'poster_small',
        'poster_medium',
        'poster_large',
        'telegram_id',
        'google_id'
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

    public const THUMB_FOLDER = 'images/user_thumbs';
    public const THUMB_URL = 'storage/' . self::THUMB_FOLDER;

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function country(): HasOneThrough
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
        return $this->hasMany(FavoriteFilm::class)
            ->orderBy('favorite_films.updated_at', 'desc');
    }

    public function stars(): HasMany
    {
        return $this->hasMany(FilmStar::class);
    }

    public function favorite_films_films(): HasManyThrough
    {
        return $this->hasManyThrough(
            Film::class,
            FavoriteFilm::class,
            'user_id',
            'id',
            'id',
            'film_id'
        )->orderBy('favorite_films.updated_at', 'desc');
    }

    public function chats(): ?BelongsToMany
    {
        return $this->belongsToMany(Chat::class)->orderBy('updated_at', 'desc');
    }

    public function like($comment_id, $is_like = true)
    {
        $like = Like::firstOrNew([
            'user_id' => Auth::id(),
            'comment_id' => $comment_id
        ]);

        $like->is_like = $is_like;
        $like->save();

        return $like;
    }

    public function unlike($comment_id)
    {
        $like = Like::where('user_id', Auth::id())
            ->where('comment_id', $comment_id);

        $like->delete();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(UserContact::class);
    }

    public function contact_users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            UserContact::class,
            'user_id',
            'id',
            'id',
            'contact_id'
        );
    }

    public function contact_requests_incomes_pivot(): HasMany
    {
        return $this->hasMany(UserContactRequest::class, 'contact_id');
    }

    public function contact_requests_outcomes_pivot(): HasMany
    {
        return $this->hasMany(UserContactRequest::class, 'user_id');
    }

    public function contact_requests_incomes(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            UserContactRequest::class,
            'contact_id',
            'id',
            'id',
            'user_id'
        );
    }

    public function contact_requests_outcomes(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            UserContactRequest::class,
            'user_id',
            'id',
            'id',
            'contact_id'
        );
    }

    public function banned_users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            BannedUser::class,
            'user_id',
            'id',
            'id',
            'banned_user_id'
        );
    }

    protected function posterSmall(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? url($value) : '',
            set: fn ($value) => $value
        );
    }

    protected function posterMedium(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? url($value) : '',
            set: fn ($value) => $value
        );
    }

    protected function posterLarge(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? url($value) : '',
            set: fn ($value) => $value
        );
    }

    public function getFavoriteFilmIdsAttribute(): array
    {
        return $this->favorite_films_films()->pluck('film_id')->toArray();
    }

    public function savePosterThumbs($source)
    {
        $imageName = $this->id . '.webp';
        $imagePath = storage_path('app/public') . '/' . self::THUMB_FOLDER;

        ImageService::processImage($source, $imagePath . '/small', $imageName, 75, 75);
        $this->poster_small = self::THUMB_URL . '/small/' . $imageName;

        ImageService::processImage($source, $imagePath . '/medium', $imageName, 150, 150);
        $this->poster_medium = self::THUMB_URL . '/medium/' . $imageName;

        ImageService::processImage($source, $imagePath . '/large', $imageName, 300, 300);
        $this->poster_large = self::THUMB_URL . '/large/' . $imageName;

        $this->save();
    }
}
