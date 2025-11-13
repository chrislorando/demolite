<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\ResponseStatus;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'receipts';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'store_name',
        'receipt_no',
        'transaction_date',
        'total_items',
        'total_discount',
        'subtotal',
        'total_payment',
        'dpp',
        'ppn',
        'response',
        'file_name',
        'file_size',
        'file_url',
        'currency',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'total_items' => 'integer',
            'total_discount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total_payment' => 'decimal:2',
            'dpp' => 'decimal:2',
            'ppn' => 'decimal:2',
            'response' => 'array',
            'file_size' => 'integer',
            'file_url' => 'string',
            'file_name' => 'string',
            'currency' => 'string',
            'status' => ResponseStatus::class,
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReceiptItem::class, 'receipt_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
