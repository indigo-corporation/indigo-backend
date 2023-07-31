<?php

namespace App\Models\Genre;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Genre\GenreTranslation
 *
 * @property int $id
 * @property int $genre_id
 * @property string $locale
 * @property string $title
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GenreTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GenreTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GenreTranslation query()
 *
 * @mixin \Eloquent
 */
class GenreTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'genre_id',
        'locale'
    ];
}
