<?php

namespace App\Models;

use App\Models\Film\Film;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilmStar extends Model
{
    protected $fillable = [
        'user_id',
        'film_id',
        'count'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function films(): BelongsToMany
    {
        return $this->belongsTo(Film::class);
    }
}
