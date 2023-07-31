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
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Film\Film
 *
 * @property int $id
 * @property string|null $original_title
 * @property int|null $runtime
 * @property string|null $release_date
 * @property int|null $year
 * @property string|null $imdb_id
 * @property string|null $imdb_rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $shiki_id
 * @property string|null $shiki_rating
 * @property bool $is_anime
 * @property bool $is_serial
 * @property string|null $category
 * @property string|null $poster
 * @property string|null $poster_small
 * @property string|null $poster_medium
 * @property bool $is_cartoon
 * @property bool $is_hidden
 * @property int $imdb_votes
 * @property bool $has_player
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|Country[] $countries
 * @property-read \Illuminate\Database\Eloquent\Collection|FavoriteFilm[] $favorite_films
 * @property-read \Illuminate\Database\Eloquent\Collection|Genre[] $genres
 * @property-read \Illuminate\Database\Eloquent\Collection|FilmStar[] $stars
 * @property-read \App\Models\Film\FilmTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Film\FilmTranslation[] $translations
 *
 * @method static Builder|Film listsTranslations(string $translationField)
 * @method static Builder|Film newModelQuery()
 * @method static Builder|Film newQuery()
 * @method static Builder|Film notTranslatedIn(?string $locale = null)
 * @method static Builder|Film orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Film orWhereTranslationIlike($key, $value, $locale = null)
 * @method static Builder|Film orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Film orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Film query()
 * @method static Builder|Film sort($sortField, $sortDirection = 'desc')
 * @method static Builder|Film translated()
 * @method static Builder|Film translatedIn(?string $locale = null)
 * @method static Builder|Film whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Film whereTranslationIlike($key, $value, $locale = null)
 * @method static Builder|Film whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Film whereTranslationNotIlike($key, $value, $locale = null)
 * @method static Builder|Film withTranslation()
 *
 * @mixin \Eloquent
 */
class Film extends Model implements TranslatableContract
{
    use Translatable;
    use CustomTranslatableTrait;

    protected $fillable = [
        'original_title',
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
        'category',
        'is_hidden',
        'imdb_votes',
        'has_player'
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

    public const IMDB_VOTES_MIN = 1000;

    public const THUMB_FOLDER = 'images/film_thumbs';

    public const THUMB_URL = 'storage/' . self::THUMB_FOLDER;

    public const HIDDEN_COUNTRIES = ['IN', 'TR'];

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

        $query->orderBy($sortField, $sortDirection)->orderBy('id', 'desc');
    }

    public function getCategoryName(): string
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

    public function updateCategory(): void
    {
        $this->category = $this->getCategoryName();
        $this->save();
    }

    public function savePoster(): void
    {
        $tempName = 'temp_' . $this->id;
        Storage::disk('public')->put(self::THUMB_FOLDER . '/' . $tempName, file_get_contents($this->poster));

        $tempFile = storage_path('app/public') . '/' . self::THUMB_FOLDER . '/' . $tempName;

        try {
            $this->savePosterThumbs($tempFile);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete(self::THUMB_FOLDER . '/' . $tempName);

            throw $e;
        }

        Storage::disk('public')->delete(self::THUMB_FOLDER . '/' . $tempName);
    }

    public function savePosterThumbs($source): void
    {
        $imageName = $this->id . '.webp';
        $imagePath = storage_path('app/public') . '/' . self::THUMB_FOLDER;

        ImageService::processImage($source, $imagePath . '/small', $imageName, 200, 300);
        $this->poster_small = self::THUMB_URL . '/small/' . $imageName;

        ImageService::processImage($source, $imagePath . '/medium', $imageName, 400, 600);
        $this->poster_medium = self::THUMB_URL . '/medium/' . $imageName;

        $this->save();
    }

    public static function getListQuery(
        ?string $category,
        ?int $genreId,
        ?int $year,
        ?int $countryId
    ): Builder {
        $query = Film::with(['translations', 'countries'])
            ->where('films.is_hidden', '=', false);

        if ($category) {
            $query = $query->where('films.category', $category);
        }

        if ($genreId) {
            $query = $query->whereHas('genres', function ($query) use ($genreId) {
                $query->where('genres.id', $genreId);
            });
        }

        if ($year) {
            $query = $query->where('films.year', $year);
        }

        if ($countryId) {
            $query = $query->whereHas('countries', function ($q) use ($countryId) {
                $q->where('countries.id', $countryId);
            });
        }

        return $query;
    }
}
