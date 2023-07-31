<?php

namespace App\Models;

use App\Models\Film\Film;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\FilmStar
 *
 * @property int $id
 * @property int $user_id
 * @property int $film_id
 * @property int|null $count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Film $film
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FilmStar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FilmStar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FilmStar query()
 *
 * @mixin \Eloquent
 */
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

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }
}
