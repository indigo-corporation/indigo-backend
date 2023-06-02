<?php

namespace App\Models;

use App\Models\Film\Film;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function films(): ?BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    public function user(): ?BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent_comments(): ?HasMany
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }

    public function likes(): ?HasMany
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
