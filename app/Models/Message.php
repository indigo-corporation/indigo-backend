<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $user_id
 * @property int $chat_id
 * @property bool $is_seen
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chat $chat
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 *
 * @mixin \Eloquent
 */
class Message extends Model
{
    protected $fillable = [
        'user_id',
        'chat_id',
        'is_seen',
        'body',
        'created_at',
        'updated_at'
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
