<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ResponseStatus;

class Document extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'size',
        'instructions',
        'url',
        'status',
        'response',
        'user_id',
    ];

    protected $casts = [
        'response' => 'array',
        'status' => ResponseStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
