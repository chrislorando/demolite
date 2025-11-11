<div>
    <div class="flex items-center justify-between">
        @include('partials.general-heading', ['heading' => 'CV Screening', 'subheading' => 'Uploaded CVs and screening results.'])
    </div>

    <livewire:cv-screening.table />

    {{-- Form modal (child component inside modal) --}}
    <flux:modal name="cv-form-modal" class="md:w-2xl lg:w-3xl">
        <livewire:cv-screening.form />
    </flux:modal>


</div>
