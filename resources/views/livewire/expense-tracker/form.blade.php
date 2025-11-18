<div class="rounded-md">
    <h4 class="mb-3 font-semibold text-zinc-800 dark:text-zinc-100">Upload Shopping Receipt</h4>

    <form wire:submit.prevent="submit" class="space-y-4">
        <div
            x-data="{ uploading: false, progress: 0 }"
            x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-cancel="uploading = false"
            x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress"
        >
            <flux:field>
                <flux:label>File (jpg, png, pdf)</flux:label>
                <flux:input type="file" wire:model="file" accept="image/*,.pdf" class="max-w-full truncate" />
                <flux:error name="file" />
            </flux:field>

            <div x-show="uploading" class="flex items-center gap-2 mt-2">
                <progress max="100" x-bind:value="progress" class="w-full h-2"></progress>
                <flux:button type="button" variant="danger" wire:click="$cancelUpload('file')" size="sm">
                    <flux:icon name="trash" class="w-4 h-4" />
                </flux:button>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button type="submit" variant="primary">Upload</flux:button>
        </div>
    </form>
</div>
