<?php

namespace App\Models;

use App\Models\Film\Film;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
