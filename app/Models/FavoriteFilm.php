<?php

namespace App\Models;

use App\Models\Film\Film;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\FavoriteFilm
 *
 * @property int $id
 * @property int $user_id
 * @property int $film_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Film|null $films
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FavoriteFilm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FavoriteFilm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FavoriteFilm query()
 *
 * @mixin \Eloquent
 */
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

    public function films(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }
}
