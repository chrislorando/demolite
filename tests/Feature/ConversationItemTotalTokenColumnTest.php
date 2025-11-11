<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

it('has total_token column on conversation_items table', function () {
    // Run migrations so the new migration is applied in test environment
    Artisan::call('migrate', ['--force' => true]);

    expect(Schema::hasColumn('conversation_items', 'total_token'))->toBeTrue();
});
