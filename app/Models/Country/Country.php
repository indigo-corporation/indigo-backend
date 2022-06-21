<?php

namespace App\Models\Country;

use App\Models\Film\Film;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Country extends Model implements TranslatableContract
{
    use Translatable;

    public $timestamps = false;
    public $translationForeignKey = 'country_id';

    protected $fillable = [
        'code',
        'name',
        'title'
    ];

    public $translatedAttributes = [
        'title'
    ];

    public function films(): ?BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
