<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\UserContactRequest
 *
 * @property int $id
 * @property int $user_id
 * @property int $contact_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $contact
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserContactRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserContactRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserContactRequest query()
 *
 * @mixin \Eloquent
 */
class UserContactRequest extends Model
{
    protected $fillable = [
        'user_id',
        'contact_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
