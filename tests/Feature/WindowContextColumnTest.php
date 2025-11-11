<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

it('has window_context column on models table', function () {
    // Run migrations (fresh) so the new migration is applied in test environment
    Artisan::call('migrate', ['--force' => true]);

    expect(Schema::hasColumn('models', 'window_context'))->toBeTrue();
});
