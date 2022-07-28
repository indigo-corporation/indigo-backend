<?php

namespace App\Models\Film;

use App\Http\Traits\CustomTranslatableTrait;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Genre\Genre;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Film extends Model implements TranslatableContract
{
    use Translatable;
    use CustomTranslatableTrait;

    protected $fillable = [
        'original_title',
        'original_language',
        'release_date',
        'year',
        'runtime',
        'poster_url',
        'imdb_id',
        'imdb_rating',
        'shiki_id',
        'shiki_rating',
        'is_anime',
        'is_serial',
        'title',
        'overview'
    ];

    public $translatedAttributes = [
        'title',
        'overview'
    ];

    public function genres(): ?BelongsToMany
    {
        return $this->belongsToMany(Genre::class)
            ->with('translations');
    }

    public function countries(): ?BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    public function comments (): HasMany
    {
        return $this->hasMany(Comment::class)->where('type', '=', Comment::COMMENT_TYPE_FILM);
    }
}
