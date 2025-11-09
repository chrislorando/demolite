<?php

use App\Models\Personalization;
use function Livewire\Volt\{state, mount, action};

state([
    'tone' => 'default',
    'instructions' => '',
    'nickname' => '',
    'occupation' => '',
    'about' => '',
    'is_active' => true,
]);

mount(function () {
    $personalization = auth()->user()->personalization ?? new Personalization([
        'tone' => 'default',
        'status' => 'active',
    ]);

    $this->tone = $personalization->tone;
    $this->instructions = $personalization->instructions ?? '';
    $this->nickname = $personalization->nickname ?? '';
    $this->occupation = $personalization->occupation ?? '';
    $this->about = $personalization->about ?? '';
    $this->is_active = $personalization->status === 'active';
});

$save = action(function () {
    $data = [
        'tone' => $this->tone,
        'instructions' => $this->instructions,
        'nickname' => $this->nickname,
        'occupation' => $this->occupation,
        'about' => $this->about,
        'status' => $this->is_active ? 'active' : 'inactive',
    ];

    auth()->user()->personalization()->updateOrCreate([], $data);

    session()->flash('message', 'Personalization updated successfully.');
});

?>
<section class="w-full">
     @include('partials.settings-heading')

    <x-settings.layout :heading="__('Personalization')" :subheading="__('Customize your AI interaction preferences')">
        <form wire:submit="save" class="my-6 w-full space-y-6">
            <flux:field variant="inline">
                <flux:label>Enable customization</flux:label>
                <flux:switch wire:model="is_active" />
            </flux:field>

            <flux:fieldset>
                <flux:legend>Tone</flux:legend>

                <flux:radio.group wire:model="tone" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <flux:radio
                        value="default"
                        label="Default"
                        description="Standard conversational tone"
                        checked
                    />
                    <flux:radio
                        value="listener"
                        label="Listener"
                        description="Empathetic and attentive tone"
                    />
                    <flux:radio
                        value="robot"
                        label="Robot"
                        description="Precise and mechanical tone"
                    />
                    <flux:radio
                        value="nerd"
                        label="Nerd"
                        description="Technical and detailed tone"
                    />
                    <flux:radio
                        value="cynic"
                        label="Cynic"
                        description="Sarcastic and critical tone"
                    />
                </flux:radio.group>
            </flux:fieldset>

            <flux:field>
                <flux:label>Instructions</flux:label>
                <flux:textarea wire:model="instructions" placeholder="Additional behaviour, style, and tone preferences"></flux:textarea>
            </flux:field>

            <flux:field>
                <flux:label>Nickname</flux:label>
                <flux:input wire:model="nickname" placeholder="What should ChatGpt call you?"></flux:input>
            </flux:field>

            <flux:field>
                <flux:label>Occupation</flux:label>
                <flux:input wire:model="occupation" placeholder="Your occupation"></flux:input>
            </flux:field>

            <flux:field>
                <flux:label>About</flux:label>
                <flux:textarea wire:model="about" placeholder="Interests, hobbies, and anything else you'd like to share"></flux:textarea>
            </flux:field>

            <flux:spacer />

            <div class="flex">
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>

        @if (session('message'))
            <flux:callout variant="success" class="mt-4">
                {{ session('message') }}
            </flux:callout>
        @endif
    </x-settings.layout>
</section>