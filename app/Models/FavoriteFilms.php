<?php

namespace App\Models;

use App\Models\Film\Film;
use Illuminate\Database\Eloquent\Model;

class FavoriteFilms extends Model
{
    protected $fillable = [
        'user_id',
        'film_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function films()
    {
        return $this->belongsToMany(Film::class);
    }
}
