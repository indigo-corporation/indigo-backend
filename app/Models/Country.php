<?php

namespace App\Models;

use App\Models\Film\Film;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Country
 *
 * @property int $id
 * @property string $iso2
 * @property string $name
 * @property int $status
 * @property string $phone_code
 * @property string $iso3
 * @property string $region
 * @property string $subregion
 * @property string $slug
 * @property-read \Illuminate\Database\Eloquent\Collection|Film[] $films
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|Country findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Country withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @mixin \Eloquent
 */
class Country extends Model
{
    use Sluggable;

    public $timestamps = false;

    protected $fillable = [
        'iso2',
        'name',
        'status',
        'phone_code',
        'iso3',
        'region',
        'subregion',
        'slug'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name', 'id'],
                'separator' => '-',
                'unique' => false
            ],
        ];
    }

    public function films(): ?BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }

    public static function getList()
    {
        return Cache::remember('country-list', now()->addHours(6), function () {
            return self::whereHas('films', function ($q) {
                $q->where('imdb_votes', '>=', Film::IMDB_VOTES_MIN);
            })->get();
        });
    }

}
