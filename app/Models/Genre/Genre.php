<?php

namespace App\Models\Genre;

use App\Http\Traits\CustomTranslatableTrait;
use App\Models\Film\Film;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Genre\Genre
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_anime
 * @property bool $is_hidden
 * @property-read \Illuminate\Database\Eloquent\Collection|Film[] $films
 * @property-read \App\Models\Genre\GenreTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Genre\GenreTranslation[] $translations
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Genre findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre orWhereTranslationIlike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Genre query()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre translated()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereTranslationIlike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereTranslationNotIlike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre withTranslation()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 *
 * @mixin \Eloquent
 */
class Genre extends Model implements TranslatableContract
{
    use Translatable;
    use Sluggable;
    use CustomTranslatableTrait;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'title',
        'is_anime',
        'is_genre'
    ];

    public $translatedAttributes = [
        'title'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name', 'id'],
                'separator' => '-',
                'unique' => true
            ],
        ];
    }

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }

    public static function getList(bool $is_anime)
    {
        $key = 'genre-list:' . ($is_anime ? 'anime' : 'not_anime');

        return Cache::remember($key, now()->addHours(6), function () use ($is_anime) {
            return Genre::where('is_anime', $is_anime)->get();
        });
    }
}
