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
        Schema::create('voice_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('voice_note_id')->constrained('voice_notes');
            $table->text('description');
            $table->enum('status', ['todo', 'done']);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voice_note_items');
    }
};
