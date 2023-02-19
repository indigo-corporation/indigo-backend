<?php

namespace App\Models\Film;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class FilmTranslation extends Model
{
    use Sluggable;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'overview',
        'slug'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'film_id'],
                'separator' => '-',
                'unique' => false
            ],
        ];
    }
}
