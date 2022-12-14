<?php

namespace App\Models\Genre;

use App\Http\Traits\CustomTranslatableTrait;
use App\Models\Film\Film;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model implements TranslatableContract
{
    use Translatable;
    use Sluggable;
    use CustomTranslatableTrait;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'title',
        'is_anime',
        'is_genre'
    ];

    public $translatedAttributes = [
        'title'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name'],
                'separator' => '-',
                'unique' => true
            ],
        ];
    }

    public function films(): ?BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
