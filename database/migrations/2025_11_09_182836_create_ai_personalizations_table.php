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
        Schema::create('personalizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tone', ['default', 'listener', 'robot', 'nerd', 'cynic'])->default('default');
            $table->text('instructions')->nullable();
            $table->string('nickname')->nullable();
            $table->string('occupation')->nullable();
            $table->text('about')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unique('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personalizations');
    }
};
