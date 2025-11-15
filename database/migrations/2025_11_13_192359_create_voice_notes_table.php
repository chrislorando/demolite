<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('voice_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('file_name');
            $table->integer('file_size')->nullable();
            $table->string('file_url');
            $table->text('transcript')->nullable();
            $table->text('response')->nullable();
            $table->json('tags')->nullable();
            $table->integer('duration')->nullable();
            $table->enum('status', ['created', 'in_progress', 'completed', 'failed', 'incomplete', 'added']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voice_notes');
    }
};
