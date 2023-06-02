<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Like
 *
 * @property int $id
 * @property int $user_id
 * @property int $comment_id
 * @property bool $is_like
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Comment $comment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Like dislikes()
 * @method static \Illuminate\Database\Eloquent\Builder|Like likes()
 * @method static \Illuminate\Database\Eloquent\Builder|Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like query()
 * @mixin \Eloquent
 */
class Like extends Model
{
    protected $fillable = [
        'user_id',
        'comment_id',
        'is_like'
    ];

    protected $casts = [
        'is_like' => 'bool'
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLikes($query)
    {
        return $query->where('is_like', true);
    }

    public function scopeDislikes($query)
    {
        return $query->where('is_like', false);
    }
}
