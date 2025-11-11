<div wire:poll.15000ms>
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <flux:input placeholder="Search..." wire:model.live.debounce.300ms="search" />
        </div>

        <div class="flex items-center gap-2">
            <flux:button wire:click="openUpload">Upload CV</flux:button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-zinc-200 dark:border-zinc-700 overflow-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-2 text-left">Job Position</th>
                        <th class="px-4 py-2 text-left">Skill</th>
                        <th class="px-4 py-2 text-left">Experience</th>
                        <th class="px-4 py-2 text-left">Education</th>
                        <th class="px-4 py-2 text-left">Overall</th>
                        <th class="px-4 py-2 text-left">Recommended</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Uploaded</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cvs as $cv)
                    <tr class="border-t border-zinc-100 dark:border-zinc-800">
                        <td class="px-4 py-3">
                           {{ $cv->job_position ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-right">{{ number_format($cv->skill_match, 0, "", "") }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($cv->experience_match, 0, "", "") }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($cv->education_match, 0, "", "") }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($cv->overall_score, 0, "", "") }}</td>

                        <td class="px-4 py-3 text-center">
                            @if($cv->is_recommended)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-green-100 text-green-800">Yes</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-zinc-100 text-zinc-800">No</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            @php
                                $statusKey = $cv->status?->value ?? ($cv->status ?? 'created');
                                $status = \App\Enums\DocumentStatus::fromString($statusKey);
                            @endphp

                            <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $status->color() }} {{ $cv->status->value=='in_progress' ? 'animate-pulse' : '' }}">{{ $status->label() }}</span>
                        </td>

                        <td class="px-4 py-3">{{ $cv->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-nowrap">
                            <flux:button size="sm" variant="primary" :href="route('cv-screening.show', $cv)" icon="eye"></flux:button>
                            <flux:button size="sm" variant="danger" wire:click="confirmDelete('{{ $cv->id }}')" icon="trash"></flux:button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-6 text-center text-zinc-400">No CVs yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $cvs->links('pagination::tailwind') }}
    </div>

    {{-- Delete confirmation modal --}}
    <flux:modal name="delete-cv-modal" class="md:w-96">
        <div class="space-y-4">
            <flux:heading size="lg">Delete CV</flux:heading>
            <flux:subheading>Are you sure you want to delete this CV? This cannot be undone.</flux:subheading>
        </div>
        <div class="flex mt-6">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="deleteCv">Delete</flux:button>
        </div>
    </flux:modal>
</div>
