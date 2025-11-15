<?php

namespace App\Livewire\Transcribe;

use App\Models\VoiceNote;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public int $totalThisMonth = 0;

    public int $transactionsThisMonth = 0;

    public int $avgDuration = 0;

    protected $listeners = [
        'refreshTranscribes' => '$refresh',
        'transcribeCreated' => 'refreshSummary',
        'transcribeDeleted' => 'refreshSummary',
        'transcribeUpdated' => 'refreshSummary',
    ];

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->refreshSummary();
    }

    public function updatingStartDate(): void
    {
        $this->resetPage();
        $this->refreshSummary();
    }

    public function updatingEndDate(): void
    {
        $this->resetPage();
        $this->refreshSummary();
    }

    public function showDetail(string $id): void
    {
        $this->dispatch('showTranscribeDetail', voiceNoteId: $id);
    }

    public function confirmDelete(string $id): void
    {
        $this->dispatch('delete-voice-note', voiceNoteId: $id);
    }

    public function openUpload(): void
    {
        $this->redirect(route('transcribe.create'), navigate: true);
    }

    public function refreshSummary(): void
    {
        $query = VoiceNote::query();

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        if ($this->startDate && $this->endDate) {
            try {
                $start = Carbon::parse($this->startDate)->startOfDay();
                $end = Carbon::parse($this->endDate)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) {
                // ignore parse errors
            }
        } elseif ($this->startDate) {
            try {
                $start = Carbon::parse($this->startDate)->startOfDay();
                $query->where('created_at', '>=', $start);
            } catch (\Throwable $e) {
                // ignore
            }
        } elseif ($this->endDate) {
            try {
                $end = Carbon::parse($this->endDate)->endOfDay();
                $query->where('created_at', '<=', $end);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $this->totalThisMonth = (int) $query->count();
        $this->transactionsThisMonth = (int) $query->count();
        $avgDuration = $query->avg('duration');
        $this->avgDuration = $avgDuration ? (int) $avgDuration : 0;
    }

    public function render()
    {
        $this->refreshSummary();

        $pendingVoiceNotes = VoiceNote::query()
            ->withCount('items')
            ->whereIn('status', ['created', 'in_progress'])
            ->latest()
            ->get();

        $query = VoiceNote::query()->withCount('items')->latest();

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        if ($this->startDate && $this->endDate) {
            try {
                $start = Carbon::parse($this->startDate)->startOfDay();
                $end = Carbon::parse($this->endDate)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) {
                // ignore parse errors
            }
        } elseif ($this->startDate) {
            try {
                $start = Carbon::parse($this->startDate)->startOfDay();
                $query->where('created_at', '>=', $start);
            } catch (\Throwable $e) {
                // ignore
            }
        } elseif ($this->endDate) {
            try {
                $end = Carbon::parse($this->endDate)->endOfDay();
                $query->where('created_at', '<=', $end);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $voiceNotes = $query->paginate($this->perPage);

        return view('livewire.transcribe.table', [
            'voiceNotes' => $voiceNotes,
            'pendingVoiceNotes' => $pendingVoiceNotes,
        ]);
    }
}
