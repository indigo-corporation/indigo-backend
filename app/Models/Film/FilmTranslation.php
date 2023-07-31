<?php

namespace App\Models\Film;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Film\FilmTranslation
 *
 * @property int $id
 * @property int $film_id
 * @property string $locale
 * @property string $title
 * @property string|null $overview
 * @property string|null $slug
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FilmTranslation findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|FilmTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FilmTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FilmTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|FilmTranslation withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 *
 * @mixin \Eloquent
 */
class FilmTranslation extends Model
{
    use Sluggable;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'overview',
        'slug'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'film_id'],
                'separator' => '-',
                'unique' => false
            ],
        ];
    }
}
