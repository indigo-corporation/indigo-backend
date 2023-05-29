<?php

namespace App\Models\Film;

use App\Http\Traits\CustomTranslatableTrait;
use App\Models\Comment;
use App\Models\Country;
use App\Models\FavoriteFilm;
use App\Models\FilmStar;
use App\Models\Genre\Genre;
use App\Services\ImageService;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Film extends Model implements TranslatableContract
{
    use Translatable;
    use CustomTranslatableTrait;

    protected $fillable = [
        'original_title',
        'original_language',
        'release_date',
        'year',
        'runtime',
        'poster',
        'poster_small',
        'poster_medium',
        'imdb_id',
        'imdb_rating',
        'shiki_id',
        'shiki_rating',
        'is_anime',
        'is_serial',
        'is_cartoon',
        'category'
    ];

    public $translatedAttributes = [
        'title',
        'overview',
        'slug'
    ];

    protected $with = [
        'genres'
    ];

    public const CATEGORY_FILM = 'film';

    public const CATEGORY_SERIAL = 'serial';

    public const CATEGORY_ANIME = 'anime';

    public const CATEGORY_CARTOON = 'cartoon';

    public const CATEGORIES = [
        self::CATEGORY_FILM,
        self::CATEGORY_SERIAL,
        self::CATEGORY_ANIME,
        self::CATEGORY_CARTOON
    ];

    public const SORT_DATE = 'release_date';

    public const SORT_IMDB = 'imdb_rating';

    public const SORT_SHIKI = 'shiki_rating';

    public const SORT_FIELDS = [
        self::SORT_DATE,
        self::SORT_IMDB,
        self::SORT_SHIKI
    ];

    public const SORT_DIRECTIONS = [
        'desc',
        'asc'
    ];

    public const SORT_FIELD = self::SORT_DATE;

    public const SORT_DIRECTION = 'desc';

    public const THUMB_FOLDER = 'images/film_thumbs';

    public const THUMB_URL = 'storage/' . self::THUMB_FOLDER;

    public function genres(): ?BelongsToMany
    {
        return $this->belongsToMany(Genre::class)
            ->with('translations');
    }

    public function countries(): ?BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('type', '=', Comment::COMMENT_TYPE_FILM);
    }

    public function favorite_films(): ?hasMany
    {
        return $this->hasMany(FavoriteFilm::class);
    }

    public function stars(): ?hasMany
    {
        return $this->hasMany(FilmStar::class);
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

    public function scopeSort(Builder $query, $sortField, $sortDirection = 'desc'): void
    {
        if ($sortField === self::SORT_DATE) {
            $query = $query->whereNotNull('year')->orderBy('year', $sortDirection);
        } else {
            $query = $query->whereNotNull($sortField);
        }

        $query->orderBy($sortField, $sortDirection);
    }

    public function getCategoryName()
    {
        if ($this->is_anime) {
            return self::CATEGORY_ANIME;
        }

        if ($this->is_cartoon) {
            return self::CATEGORY_CARTOON;
        }

        if ($this->is_serial) {
            return self::CATEGORY_SERIAL;
        }

        return self::CATEGORY_FILM;
    }

    public function updateCategory()
    {
        $this->category = $this->getCategoryName();
        $this->save();
    }

    public function savePosterThumbs($source)
    {
        $imageName = $this->id . '.webp';
        $imagePath = storage_path('app/public') . '/' . self::THUMB_FOLDER;

        ImageService::processImage($source, $imagePath . '/small', $imageName, 200, 300);
        $this->poster_small = self::THUMB_URL . '/small/' . $imageName;

        ImageService::processImage($source, $imagePath . '/medium', $imageName, 400, 600);
        $this->poster_medium = self::THUMB_URL . '/medium/' . $imageName;

        $this->save();
    }
}
