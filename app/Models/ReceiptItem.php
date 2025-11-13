<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptItem extends Model
{
    use HasFactory;

    protected $table = 'receipt_items';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'receipt_id',
        'name',
        'quantity',
        'unit_price',
        'total_price',
        'discount',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'discount' => 'decimal:2',
        ];
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'receipt_id', 'id');
    }
}
