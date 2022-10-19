<?php

namespace App\Models;

use App\Models\Film\Film;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FavoriteFilm extends Model
{
    protected $fillable = [
        'user_id',
        'film_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
