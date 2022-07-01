<?php

namespace App\Models;

use App\Models\Film\Film;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 * @property int user_id
 * @property int film_id
 * @property string body
 * @property string type
 * @property int parent_comment_id

 * @mixin Eloquent
 * @mixin Builder
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

    public function films(): ?BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    public function users(): ?BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent_comments(): ?HasMany
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }
}
