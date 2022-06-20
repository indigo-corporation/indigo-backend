<?php

namespace App\Models\Country;

use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'country_id',
        'locale'
    ];
}
