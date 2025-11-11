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
        Schema::create('curriculum_vitaes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('model_id')->nullable();
            $table->foreign('model_id')->references('id')->on('models');

            $table->text('job_offer')->nullable();
            $table->string('job_position')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_url')->nullable();
       
            $table->decimal('skill_match', 8, 4)->nullable();
            $table->decimal('experience_match', 8, 4)->nullable();
            $table->decimal('education_match', 8, 4)->nullable();
            $table->decimal('overall_score', 8, 4)->nullable();

            $table->text('summary')->nullable();
            $table->text('suggestion')->nullable();
            $table->text('cover_letter')->nullable();

            $table->boolean('is_recommended')->default(false);

            $table->json('response')->nullable();

            $table->string('status')->default('created');

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_vitaes');
    }
};
