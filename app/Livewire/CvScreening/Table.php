<?php

namespace App\Livewire\CvScreening;

use App\Models\CurriculumVitae;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public string $search = '';
    
    public ?string $deletingCvId = null;

    protected $updatesQueryString = ['search'];

    protected $listeners = [
        'cv-created' => '$refresh',
        'cv-deleted' => '$refresh',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function view(string $id): void
    {
        $this->dispatch('view-cv', cvId: $id);
    }

    public function openUpload(): void
    {
        $this->dispatch('cvUpload:open');
    }

    public function viewCv(string $cvId): void
    {
        $this->selectedCvId = $cvId;
        $this->redirectRoute('cv-screening', $this->selectedCvId, true, true);
    }

    public function confirmDelete(string $cvId): void
    {
        $this->deletingCvId = $cvId;
        $this->modal('delete-cv-modal')->show();
    }

    public function deleteCv(): void
    {
        $cv = CurriculumVitae::find($this->deletingCvId);

        if ($cv) {
            try {
                if ($cv->file_url) {
                    $urlPath = parse_url($cv->file_url, PHP_URL_PATH) ?: '';
                    $key = ltrim($urlPath, '/');

                    $bucket = config('filesystems.disks.s3.bucket');
                    if ($bucket && str_starts_with($key, $bucket.'/')) {
                        $key = substr($key, strlen($bucket) + 1);
                    }

                    if ($key) {
                        \Illuminate\Support\Facades\Storage::disk('s3')->delete($key);
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to delete CV file from s3', ['id' => $cv->id, 'error' => $e->getMessage()]);
            }

            $cv->delete();
        }

        $this->modal('delete-cv-modal')->close();
        $this->dispatch('cv-deleted');
    }

    public function render()
    {
        $cvs = CurriculumVitae::query()
            ->where('user_id', auth()->id())
            ->when($this->search, function($q){
                $q->whereLike('file_name',  '%'.$this->search.'%')
                ->orWhereLike('summary',  '%'.$this->search.'%')
                ->orWhereLike('response',  '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.cv-screening.table', [
            'cvs' => $cvs,
        ]);
    }
}
