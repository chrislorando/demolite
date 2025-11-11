<div class="w-full">
    <div class="space-y-4 max-w-3xl">
        <div>
            <flux:heading size="lg">Upload CV</flux:heading>
            <flux:subheading>Upload a PDF of the CV</flux:subheading>
        </div>

        <form wire:submit.prevent="submit" class="space-y-4">
            <flux:field>
                <flux:label>Model</flux:label>
                <flux:select wire:model="model_id">
                    <option value="">Use default model</option>
                    @foreach($models as $m)
                        <option value="{{ $m->id }}">{{ $m->id }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="model_id" />
            </flux:field>

            <flux:field>
                <flux:label>Job Offer (link or text)</flux:label>
                <flux:textarea wire:model="job_offer" rows="4" description="Better result with text." />
                {{-- <flux:error name="job_offer" /> --}}
            </flux:field>

            <flux:field>
                <flux:label>PDF File</flux:label>
                <flux:input type="file" wire:model="file" accept="application/pdf" />
                <flux:error name="file" />
            </flux:field>
        
            <div class="flex items-center gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit">Upload</flux:button>
            </div>
        </form>
    </div>
</div>
