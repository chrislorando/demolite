<?php

use App\Models\User;
use function Pest\Laravel\actingAs;

it('shows documents page to authenticated user', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('/documents-verifier')
        ->assertSuccessful()
        ->assertSeeText('Documents');
});
