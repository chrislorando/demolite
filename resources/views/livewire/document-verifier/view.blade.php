<div class="space-y-4">
    @if($document)
        <div>
            <flux:heading size="lg">
                @if ($document->url)
                    <a href="{{ $document->url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ $document->name }}</a>
                @endif
            </flux:heading>
            <flux:subheading>Uploaded {{ $document->created_at }}</flux:subheading>
        </div>

        <div>
            <flux:field>
                <div class="flex items-center justify-between">
                    <flux:label>Verification Result</flux:label>
                    <div>
                        @php
                            $statusKey = $document->status?->value ?? ($document->status ?? 'created');
                            $status = \App\Enums\DocumentStatus::fromString($statusKey);
                        @endphp

                        <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $status->color() }}">{{ $status->label() }}</span>
                    </div>
                </div>
            </flux:field>
        </div>

         <div>
            <flux:field>
                <flux:label>Instructions</flux:label>
                <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">{{ $document->instructions ?? '-' }}</div>
            </flux:field>
        </div>


        <div class="shiki mt-3">
            <x-markdown :anchors="false"
                        :options="[
                            'commonmark' => [
                                'enable_em' => true,
                                'enable_strong' => true,
                                'use_asterisk' => true,
                                'use_underscore' => true,
                            ],
                            'html_input' => 'strip',
                            'max_nesting_level' => 10,
                            'renderer' => [
                                'block_separator' => PHP_EOL,
                                'inner_separator' => PHP_EOL,
                                'soft_break' => PHP_EOL,
                            ],
                        ]"
                        theme="github-dark">
                {!! $document->response !!}
            </x-markdown>
        </div>
    @else
        <div class="p-4 text-zinc-500">No document selected</div>
    @endif

    <div class="flex mt-4">
        <flux:spacer />
        <flux:modal.close>
            <flux:button variant="ghost">Close</flux:button>
        </flux:modal.close>
    </div>
</div>
