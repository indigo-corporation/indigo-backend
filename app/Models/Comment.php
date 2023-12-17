<?php

namespace App\Models;

use App\Models\Film\Film;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Comment
 *
 * @property int $id
 * @property int $user_id
 * @property int $film_id
 * @property string $body
 * @property string $type
 * @property int|null $parent_comment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Film|null $films
 * @property-read mixed $dislikes_count
 * @property-read mixed $likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $parent_comments
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 *
 * @mixin \Eloquent
 */
class Comment extends Model
{
    use HasFactory;

    public const COMMENT_TYPE_FILM = 'FilmComment';

    public const COMMENT_TYPE_ANSWER = 'AnswerComment';

    protected $fillable = [
        'user_id',
        'film_id',
        'body',
        'type',
        'parent_comment_id'
    ];

    public function films(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent_comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->likes()->count() ?? 0;
    }

    public function getDislikesCountAttribute()
    {
        return $this->likes()->dislikes()->count() ?? 0;
    }

    public function getMyLike()
    {
        $user = auth('sanctum')->user();

        return $user
            ? $this->likes()->where('user_id', $user->id)->first()
            : null;
    }
}
