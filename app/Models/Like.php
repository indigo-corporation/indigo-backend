<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
