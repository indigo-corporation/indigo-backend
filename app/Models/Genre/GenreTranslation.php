<?php

namespace App\Models\Genre;

use Illuminate\Database\Eloquent\Model;

class GenreTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'genre_id',
        'locale'
    ];
}
