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
        'poster_url',
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

    const CATEGORY_FILM = 'film';
    const CATEGORY_SERIAL = 'serial';
    const CATEGORY_ANIME = 'anime';
    const CATEGORY_CARTOON = 'cartoon';

    const THUMB_FOLDER = 'images/film_thumbs';

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

    public static function typeQuery($query, $type)
    {
        if ($type === self::CATEGORY_FILM) {
            $query = $query->where('is_anime', false)->where('is_serial', false)
                ->whereDoesnthave('genres', function ($q) {
                    $q->where('name', 'animation');
                });
        }

        if ($type === self::CATEGORY_SERIAL) {
            $query = $query->where('is_anime', false)->where('is_serial', true)
                ->whereDoesnthave('genres', function ($q) {
                    $q->where('name', 'animation');
                });
        }

        if ($type === self::CATEGORY_ANIME) {
            $query = $query->where('is_anime', true);
        }

        if ($type === self::CATEGORY_CARTOON) {
            $query = $query->where('is_anime', false)->whereHas('genres', function ($q) {
                $q->where('name', 'animation');
            });
        }

        return $query;
    }

    public function getCategoryName()
    {
        if ($this->is_anime) return self::CATEGORY_ANIME;

        if ($this->genres->contains('name', 'animation')) {
            return self::CATEGORY_CARTOON;
        }

        if ($this->is_serial) return self::CATEGORY_SERIAL;

        return self::CATEGORY_FILM;
    }

    public function updateCategory()
    {
        $this->category = $this->getCategoryName();
        $this->save();
    }

    public function savePosterThumb($url)
    {
        $tempName = 'temp_' . $this->id;
        Storage::disk('public')->put(self::THUMB_FOLDER . '/' . $tempName, file_get_contents($url));

        $imageName = $this->id . '.jpg';
        Image::configure(['driver' => 'imagick']);
        $imagePath = storage_path('app/public') . '/' . self::THUMB_FOLDER;
        $tempFile = $imagePath . '/' . $tempName;

        $smallPath = $imagePath . '/small';
        if (!file_exists($smallPath)) {
            mkdir($smallPath, 0775, true);
        }
        Image::make($tempFile)->encode('jpg')->resize(193, 272)
            ->save($smallPath . '/' . $imageName);

        $mediumPath = $imagePath . '/medium';
        if (!file_exists($mediumPath)) {
            mkdir($mediumPath, 0775, true);
        }
        Image::make($tempFile)->encode('jpg')->resize(386, 544)
            ->save($mediumPath . '/' . $imageName);

//        Storage::disk('public')->delete(self::THUMB_FOLDER . '/' . $tempName);

        ImageOptimizer::optimize($tempFile, $imagePath . '/optimized.jpeg');
        ImageOptimizer::optimize($smallPath . '/' . $imageName, $smallPath . '/optimized.jpeg');
        ImageOptimizer::optimize($mediumPath . '/' . $imageName, $mediumPath . '/optimized.jpeg');

        dd(
            Storage::disk('public')->url('images/film_thumbs/small/' . $imageName),
            url('images/film_thumbs/small/' . $imageName),
            url('images/film_thumbs/medium/' . $imageName)
        );
    }
}
