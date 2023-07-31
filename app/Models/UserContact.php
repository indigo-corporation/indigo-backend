<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\UserContact
 *
 * @property int $id
 * @property int $user_id
 * @property int $contact_id
 * @property-read \App\Models\User $contact
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserContact query()
 *
 * @mixin \Eloquent
 */
class UserContact extends Model
{
    protected $fillable = [
        'user_id',
        'contact_id'
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
