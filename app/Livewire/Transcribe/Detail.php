<?php

namespace App\Livewire\Transcribe;

use App\Models\VoiceNote;
use App\Models\VoiceNoteItem;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Detail extends Component
{
    public ?VoiceNote $voiceNote = null;

    public ?string $voiceNoteId = null;

    public int $item_id = 0;

    public string $item_description = '';

    public string $item_status = 'todo';

    public ?string $item_due_date = null;

    public function mount(VoiceNote $voiceNote): void
    {
        $this->voiceNote = $voiceNote;
    }

    protected function rules(): array
    {
        return [
            'item_description' => 'required|string',
            'item_status' => 'required|in:todo,done',
            'item_due_date' => 'nullable|date',
        ];
    }

    public function loadVoiceNote(string $id): void
    {
        $this->voiceNote = VoiceNote::with('items')->findOrFail($id);
    }

    public function addItem(): void
    {
        $this->validate();

        VoiceNoteItem::create([
            'voice_note_id' => $this->voiceNote->id,
            'description' => $this->item_description,
            'status' => $this->item_status,
            'due_date' => $this->item_due_date,
        ]);

        $this->dispatch('transcribeUpdated');
        $this->resetItemForm();
    }

    public function editItem(int $id): void
    {
        $item = VoiceNoteItem::findOrFail($id);

        $this->item_id = $item->id;
        $this->item_description = $item->description;
        $this->item_status = $item->status->value;
        $this->item_due_date = $item->due_date ? $item->due_date->format('Y-m-d') : null;
    }

    public function updateItem(): void
    {
        $this->validate();

        $item = VoiceNoteItem::findOrFail($this->item_id);
        $item->update([
            'description' => $this->item_description,
            'status' => $this->item_status,
            'due_date' => $this->item_due_date,
        ]);

        $this->dispatch('transcribeUpdated');
        $this->resetItemForm();
    }

    public function resetItemForm(): void
    {
        $this->item_id = 0;
        $this->item_description = '';
        $this->item_status = 'todo';
        $this->item_due_date = null;
    }

    public function deleteItem(int $id): void
    {
        $item = VoiceNoteItem::where('voice_note_id', $this->voiceNote?->id)
            ->whereKey($id)
            ->first();

        if (! $item) {
            return;
        }

        $item->delete();

        if ($this->item_id === $id) {
            $this->resetItemForm();
        }

        $this->dispatch('transcribeUpdated');
    }

    public function render()
    {
        return view('livewire.transcribe.detail');
    }
}
