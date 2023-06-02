<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BannedUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $banned_user_id
 * @property-read \App\Models\User $banned_user
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|BannedUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BannedUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BannedUser query()
 * @mixin \Eloquent
 */
class BannedUser extends Model
{
    protected $fillable = [
        'user_id',
        'banned_user_id'
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function banned_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
