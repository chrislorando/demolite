<?php

declare(strict_types=1);

use function Pest\Laravel\get;

it('renders landing page hero and cta elements', function () {
    $response = get('/');

    $response->assertSuccessful();
    $response->assertSee('Explore AI models', false);
    $response->assertSee('Start Exploring', false);
    $response->assertSee('AI Chatbot', false);
    $response->assertSee('File Validation', false);
    $response->assertSee('Resume Analysis', false);
});
