<?php

namespace App\Livewire\CvScreening;

use App\Models\CurriculumVitae;
use Livewire\Attributes\On;
use Livewire\Component;

class View extends Component
{
    public ?CurriculumVitae $cv = null;

    public function mount(CurriculumVitae $cv): void
    {
        $this->cv = $cv;
    }

    public function render()
    {
        return view('livewire.cv-screening.view');
    }
}
