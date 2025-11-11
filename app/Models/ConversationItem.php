<?php

namespace App\Models;

use App\Enums\ResponseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationItem extends Model
{
    /** @use HasFactory<\Database\Factories\ConversationItemFactory> */
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'model_id',
        'response_id',
        'status',
        'total_token',
    ];

    protected $casts = [
        'status' => ResponseStatus::class,
        'total_token' => 'integer',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'model_id');
    }
}