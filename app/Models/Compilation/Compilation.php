<?php

namespace App\Models\Compilation;

use App\Http\Traits\CustomTranslatableTrait;
use App\Models\Film\Film;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Compilation\Compilation
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Film[] $films
 * @property-read \App\Models\Compilation\CompilationTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Compilation\CompilationTranslation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation orWhereTranslationIlike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation translated()
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation whereTranslationIlike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation whereTranslationNotIlike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation withTranslation()
 * @method static \Illuminate\Database\Eloquent\Builder|Compilation withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @mixin \Eloquent
 */
class Compilation extends Model implements TranslatableContract
{
    use Translatable;
    use CustomTranslatableTrait;
    use Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'order'
    ];

    public $translatedAttributes = [
        'title'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'compilation_id'],
                'separator' => '-',
                'unique' => false
            ],
        ];
    }

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
