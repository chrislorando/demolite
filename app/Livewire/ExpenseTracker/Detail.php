<?php

namespace App\Livewire\ExpenseTracker;

use App\Models\Receipt;
use App\Models\ReceiptItem;
use Livewire\Component;

class Detail extends Component
{
    public ?Receipt $receipt = null;
    public ?string $receiptId = null;

    public int $item_id = 0;
    public string $item_name = '';
    public int $item_quantity = 1;
    public float $item_unit_price = 0.00;
    public float $item_discount = 0.00;

    public function mount(Receipt $receipt): void
    {
        $this->receipt = $receipt;
    }
    
    protected function rules(): array
    {
        return [
            'item_name' => 'required|string|max:255',
            'item_quantity' => 'required|integer|min:1',
            'item_unit_price' => 'required|numeric|min:0',
            'item_discount' => 'nullable|numeric|min:0',
        ];
    }

    public function loadReceipt(string $id): void
    {
        $this->receipt = Receipt::with('items')->findOrFail($id);
    }

    public function addItem(): void
    {
        $this->validate();

        ReceiptItem::create([
            'receipt_id' => $this->receipt->id,
            'name' => $this->item_name,
            'quantity' => $this->item_quantity,
            'unit_price' => $this->item_unit_price,
            'total_price' => $this->item_quantity * $this->item_unit_price,
            'discount' => $this->item_discount,
        ]);

        $this->recalculateReceiptSummary();

        $this->dispatch('expenseUpdated');
        $this->resetItemForm();
    }

    public function editItem(int $id): void
    {
        $item = ReceiptItem::findOrFail($id);

        $this->item_id = $item->id;
        $this->item_name = $item->name;
        $this->item_quantity = $item->quantity;
        $this->item_unit_price = $item->unit_price ? (float) $item->unit_price : 0.0;
        $this->item_discount = $item->discount ? (float) $item->discount : 0.0;
    }

    public function updateItem(): void
    {
        $this->validate();

        $item = ReceiptItem::findOrFail($this->item_id);
        $item->update([
            'name' => $this->item_name,
            'quantity' => $this->item_quantity,
            'unit_price' => $this->item_unit_price,
            'total_price' => $this->item_quantity * $this->item_unit_price,
            'discount' => $this->item_discount,
        ]);

        $this->recalculateReceiptSummary();

        $this->dispatch('expenseUpdated');
        $this->resetItemForm();
    }

    public function resetItemForm(): void
    {
        $this->item_id = 0;
        $this->item_name = '';
        $this->item_quantity = 1;
        $this->item_unit_price = 0.00;
        $this->item_discount = 0.00;
    }

    public function deleteItem(int $id): void
    {
        $item = ReceiptItem::where('receipt_id', $this->receipt?->id)
            ->whereKey($id)
            ->first();

        if (! $item) {
            return;
        }

        $item->delete();

        if ($this->item_id === $id) {
            $this->resetItemForm();
        }

        $this->recalculateReceiptSummary();

        $this->dispatch('expenseUpdated');
    }

    protected function recalculateReceiptSummary(): void
    {
        if (! $this->receipt) {
            return;
        }

        $this->receipt->loadMissing('items');

        $totalItems = (int) $this->receipt->items->sum('quantity');
        $subtotal = (float) $this->receipt->items->sum('total_price');
        $totalDiscount = (float) $this->receipt->items->sum('discount');
        $totalPayment = max($subtotal - $totalDiscount, 0);

        $this->receipt->update([
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'total_payment' => $totalPayment,
        ]);

        $this->receipt->refresh();
    }

    public function render()
    {
        return view('livewire.expense-tracker.detail');
    }
}
