<?php

namespace App\Models\Compilation;

use App\Http\Traits\CustomTranslatableTrait;
use App\Models\Film\Film;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Compilation extends Model implements TranslatableContract
{
    use Translatable;
    use CustomTranslatableTrait;
    use Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'order'
    ];

    public $translatedAttributes = [
        'title'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'compilation_id'],
                'separator' => '-',
                'unique' => false
            ],
        ];
    }

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
