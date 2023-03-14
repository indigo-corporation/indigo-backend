<?php

namespace App\Models\Film;

use App\Http\Traits\CustomTranslatableTrait;
use App\Models\Comment;
use App\Models\Country;
use App\Models\FavoriteFilm;
use App\Models\FilmStar;
use App\Models\Genre\Genre;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Image;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

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
        'category',
    ];

    public $translatedAttributes = [
        'title',
        'overview',
        'slug',
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

    public const SORT_FIELDS = [
        'release_date',
        'imdb_rating',
        'shiki_rating'
    ];

    public const SORT_DIRECTIONS = [
        'desc',
        'asc'
    ];

    public const THUMB_FOLDER = 'images/film_thumbs';

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
            get: fn($value) => $value ? url($value) : '',
            set: fn($value) => $value
        );
    }

    protected function posterMedium(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? url($value) : '',
            set: fn($value) => $value
        );
    }

    public function getCategoryName()
    {
        if ($this->is_anime) {
            return self::CATEGORY_ANIME;
        }

        if ($this->genres->contains('name', 'animation')) {
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

    public function savePosterThumbs($url)
    {
        if (!$url) return;

        $imageName = $this->id . '.webp';
        $tempName = 'temp_' . $this->id;
        Storage::disk('public')->put(self::THUMB_FOLDER . '/' . $tempName, file_get_contents($url));

        $imagePath = storage_path('app/public') . '/' . self::THUMB_FOLDER;
        $tempFile = $imagePath . '/' . $tempName;
        $smallPath = $imagePath . '/small';
        $mediumPath = $imagePath . '/medium';

        if (!file_exists($smallPath)) {
            mkdir($smallPath, 0775, true);
        }

        if (!file_exists($mediumPath)) {
            mkdir($mediumPath, 0775, true);
        }

        Image::configure(['driver' => 'imagick']);

        Image::make($tempFile)->encode('webp')->resize(200, 300)
            ->save($smallPath . '/' . $imageName);
        Image::make($tempFile)->encode('webp')->resize(400, 600)
            ->save($mediumPath . '/' . $imageName);

        Storage::disk('public')->delete(self::THUMB_FOLDER . '/' . $tempName);

        ImageOptimizer::optimize($smallPath . '/' . $imageName);
        ImageOptimizer::optimize($mediumPath . '/' . $imageName);

        $this->poster_small = 'storage/images/film_thumbs/small/' . $imageName;
        $this->poster_medium = 'storage/images/film_thumbs/medium/' . $imageName;

        $this->save();
    }
}
