<?php

namespace App\Models\Film;

use Illuminate\Database\Eloquent\Model;

class FilmTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'overview'
    ];
}
