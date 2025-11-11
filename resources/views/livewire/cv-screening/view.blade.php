<div class="space-y-4" wire:poll.15000ms>
    @if($cv)
        <div class="flex items-start justify-between">
            <div>
                <flux:heading size="lg">
                    @if ($cv->file_url)
                        <a href="{{ $cv->file_url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ $cv->file_name }}</a>
                    @else
                        {{ $cv->file_name }}
                    @endif
                </flux:heading>
                <flux:subheading>Uploaded {{ $cv->created_at }} | Model {{ $cv->model_id }}</flux:subheading>
            </div>

            <div class="ml-4 flex-shrink-0">
                @php
                    $statusKey = $cv->status?->value ?? ($cv->status ?? 'created');
                    $status = \App\Enums\DocumentStatus::fromString($statusKey);
                @endphp

                <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $status->color() }} {{ $cv->status->value=='in_progress' ? 'animate-pulse' : '' }}">{{ $status->label() }}</span>
            </div>
        </div>

        <div>
            <flux:field>
                <flux:label>Job Position</flux:label>
                <div class="mt-2 text-sm leading-5 text-zinc-700 dark:text-zinc-300 bg-zinc-900 p-2">{{ $cv->job_position ?? '-' }}</div>
            </flux:field>
        </div>

        <div>
            <flux:field>
                <flux:label>Job Offer</flux:label>

                {{-- Show first few paragraphs, collapse the rest --}}
                @php
                    $rawOffer = trim((string)($cv->job_offer ?? ''));
                    // Split into paragraphs by blank line(s)
                    $paragraphs = preg_split('/\r?\n\s*\r?\n/', $rawOffer);
                    $paragraphs = $paragraphs === false ? [] : array_filter(array_map('trim', $paragraphs));
                    $visibleCount = 3;
                @endphp

                @if(empty($paragraphs))
                    <div class="mt-2 text-sm leading-5 text-zinc-700 dark:text-zinc-300">-</div>
                @else
                    <div class="bg-zinc-900 p-2">
                        <div class="mt-2 text-sm leading-5 text-zinc-700 dark:text-zinc-300 space-y-3">
                            @foreach(array_slice($paragraphs, 0, $visibleCount) as $p)
                                <p class="whitespace-pre-wrap">{{ $p }}</p>
                            @endforeach
                        </div>

                        @if(count($paragraphs) > $visibleCount)
                            <details class="mt-3">
                                <summary class="cursor-pointer text-sm text-blue-600">Show {{ count($paragraphs) - $visibleCount }} more paragraph(s)</summary>
                                <div class="mt-2 text-sm leading-5 text-zinc-700 dark:text-zinc-300 space-y-3">
                                    @foreach(array_slice($paragraphs, $visibleCount) as $p)
                                        <p class="whitespace-pre-wrap">{{ $p }}</p>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    </div>
                @endif
            </flux:field>
        </div>

        <div>
            <flux:field>
                <flux:label>Score</flux:label>
                <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">
                     <table class="min-w-full text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-900">
                            <tr>
                                <th class="px-4 py-2 text-right">Skill</th>
                                <th class="px-4 py-2 text-right">Experience</th>
                                <th class="px-4 py-2 text-right">Education</th>
                                <th class="px-4 py-2 text-right">Overall</th>
                                <th class="px-4 py-2 text-center">Recommended</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800">
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
                            </tr>
                        </tbody>
                    </table>
                </div>
            </flux:field>
        </div>

        <div>
            <flux:field>
                <flux:label>Suggestion</flux:label>
                <div class="mt-2 text-sm leading-5 text-zinc-700 dark:text-zinc-300 bg-zinc-900 p-2">{{ $cv->suggestion ?? '-' }}</div>
            </flux:field>
        </div>

        <div>
            <flux:field>
                <flux:label>Summary</flux:label>
                <div class="mt-2 text-sm leading-5 text-zinc-700 dark:text-zinc-300 bg-zinc-900 p-2">{{ $cv->summary ?? '-' }}</div>
            </flux:field>
        </div>

        <div>
            <flux:field>
                <flux:label>Cover Letter</flux:label>
                <div class="mt-2 text-sm leading-5 text-zinc-700 dark:text-zinc-300 bg-zinc-900 p-2">{!! $cv->cover_letter ?? '-' !!}</div>
            </flux:field>
        </div>


        {{-- <div class="shiki mt-3">
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
                {!! $cv->response !!}
            </x-markdown>
        </div> --}}
    @else
        <div class="p-4 text-zinc-500">No CV selected</div>
    @endif
</div>
