<?php

namespace App\Livewire\CvScreening;

use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public ?string $selectedCvId = null;

    public string $search = '';

    public ?string $deletingCvId = null;

    #[On('cv-created')]
    #[On('cv-deleted')]
    public function refresh(): void
    {
        // Close the form modal when a CV is created and allow the component to re-render.
        $this->modal('cv-form-modal')->close();
    }

    #[On('cvUpload:open')]
    public function openForm(): void
    {
        $this->modal('cv-form-modal')->show();
    }

    public function render()
    {
        $cvs = \App\Models\CurriculumVitae::query()
            ->when($this->search, fn($q) => $q->where('file_name', 'like', '%'.$this->search.'%'))
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('livewire.cv-screening.index', [
            'cvs' => $cvs,
        ]);
    }
}
