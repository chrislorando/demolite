<?php

namespace App\Livewire\Transcribe;

use App\Models\VoiceNote;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public ?string $deletingVoiceNoteId = null;

    public ?string $selectedVoiceNoteId = null;

    public function render()
    {
        return view('livewire.transcribe.index');
    }

    #[On('delete-voice-note')]
    public function deleteVoiceNote(string $voiceNoteId): void
    {
        $this->deletingVoiceNoteId = $voiceNoteId;
        $this->modal('delete-voice-note-modal')->show();
    }

    #[On('showTranscribeDetail')]
    public function viewVoiceNote(string $voiceNoteId): void
    {
        $this->selectedVoiceNoteId = $voiceNoteId;
        $this->modal('transcribe-view-modal')->show();
    }

    public function confirmDelete(): void
    {
        $voiceNote = VoiceNote::find($this->deletingVoiceNoteId);

        if ($voiceNote) {
            try {
                if ($voiceNote->file_url && Storage::disk('s3')->exists($voiceNote->file_url)) {
                    Storage::disk('s3')->delete($voiceNote->file_url);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to delete voice note file', ['id' => $voiceNote->id, 'error' => $e->getMessage()]);
            }

            try {
                $voiceNote->items()->delete();
            } catch (\Throwable $e) {
                Log::warning('Failed to delete voice note items', ['id' => $voiceNote->id, 'error' => $e->getMessage()]);
            }

            $voiceNote->delete();
        }

        $this->modal('delete-voice-note-modal')->close();
        $this->dispatch('transcribeDeleted');
    }
}
