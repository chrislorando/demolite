<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content_hash',
        'share_token',
        'content',
        'title',
        'expires_at',
    ];

    protected $casts = [
        'content' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
