<?php

namespace App\Models\Genre;

use App\Models\Film\Film;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model implements TranslatableContract
{
    use Translatable;
    use Sluggable;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'title'
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

    public function films()
    {
        return $this->belongsToMany(Film::class);
    }
}
