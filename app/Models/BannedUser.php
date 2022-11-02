<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
