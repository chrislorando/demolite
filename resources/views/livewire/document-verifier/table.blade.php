<div wire:poll.15000ms>
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <flux:input placeholder="Search..." wire:model.live.debounce.300ms="search" />
        </div>

        <div class="flex items-center gap-2">
            <flux:button wire:click="openUpload">Upload Doc</flux:button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <div class="bg-white dark:bg-zinc-900 rounded-md border border-zinc-200 dark:border-zinc-700 overflow-auto">
            <table class="min-w-full text-sm">
                {{-- Isi Tabel Anda Tetap Sama --}}
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-2 text-center">#</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Size</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Uploaded</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                    <tr class="border-t border-zinc-100 dark:border-zinc-800">
                        <td class="px-4 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2">{{ $document->name }}</td>
                        <td class="px-4 py-2">{{ $document->size ? number_format($document->size / 1024, 2) . ' KB' : '-' }}</td>
                        <td class="px-4 py-2">
                            @php
                                $statusKey = $document->status?->value ?? ($document->status ?? 'created');
                                $status = \App\Enums\DocumentStatus::fromString($statusKey);
                            @endphp

                            <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $status->color() }} {{ $document->status->value=='in_progress' ? 'animate-pulse' : '' }}">{{ $status->label() }}</span>
                        </td>
                        <td class="px-4 py-2">{{ $document->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-2 text-center">
                            <flux:button size="xs" variant="primary" wire:click="view('{{ $document->id }}')" icon="eye"></flux:button>
                            <flux:button size="xs" variant="danger" wire:click="confirmDelete('{{ $document->id }}')" icon="trash"></flux:button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-zinc-400">No documents yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $documents->links('pagination::tailwind') }}
    </div>
</div>
