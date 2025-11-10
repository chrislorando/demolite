<?php


use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Livewire\Chat\SharedConversation as SharedConversationComponent;
use OpenAI\Laravel\Facades\OpenAI;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('chat/bot-ai', \App\Livewire\Chat\BotAi::class)
    ->middleware(['auth', 'verified'])
    ->name('chat.bot-ai');

Route::get('chat', function () {
    return redirect()->route('chat.bot-ai');
})->middleware(['auth', 'verified'])->name('chat');

Route::get('chat/bot-ai/new', \App\Livewire\Chat\BotAi::class)
    ->middleware(['auth', 'verified'])
    ->name('chat.bot-ai.new');

Route::get('chat/bot-ai/{id}', \App\Livewire\Chat\BotAi::class)
    ->middleware(['auth', 'verified'])
    ->name('chat.bot-ai.show');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
    Volt::route('settings/personalization', 'settings.personalization')->name('personalization.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// Public route for shared conversations
Route::get('/shared/{token}', SharedConversationComponent::class)->name('chat.shared.show');

Route::get('documents', \App\Livewire\DocumentVerifier\Index::class)
    ->middleware(['auth', 'verified'])
    ->name('documents.index');

Route::get('/pdf', function () {
 $response = OpenAI::responses()->create([
            'model' => 'gpt-4o-mini',
            'input' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'input_text',
                            'text' => "
                                You are a document validator. This document can contain various types of forms or letters.
                                Your task:
                                1. Identify information or sections that appear to be mandatory in context (e.g., the applicant's name, signature, date, identification number, or sections that are mentioned but are blank).
                                2. Determine whether the text is complete and consistent.
                                3. Provide a summary of any missing or inconsistent information.
                                3. Return the results in the following JSON format:
                                [
                                    {\"page\": <page number>, \"section\": \"<section or sentence>\", \"issue\": \"<problem>\", \"suggestion\": \"<what needs to be improved>\"}
                                ]"
                        
                        ],
                        [
                            'type' => 'input_file',
                            'file_url' => 'https://s3.demolite.my.id/demolite/employee_form_sample.pdf',
                        ],
                    ],
                ],
            ],
        ]);

        dd($response);
});