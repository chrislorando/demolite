<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Personalization extends Model
{
    protected $fillable = [
        'user_id',
        'tone',
        'instructions',
        'nickname',
        'occupation',
        'about',
        'status',
    ];

    protected $casts = [
        'tone' => 'string',
        'status' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
