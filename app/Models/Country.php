<?php

namespace App\Models;

use App\Models\Film\Film;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|Film[] $films
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @mixin \Eloquent
 */
class Country extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'iso2',
        'name',
        'status',
        'phone_code',
        'iso3',
        'region',
        'subregion'
    ];

    public function films(): ?BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
