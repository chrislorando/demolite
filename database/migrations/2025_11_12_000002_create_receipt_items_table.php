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
        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('receipt_id');
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('receipt_id')->references('id')->on('receipts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_items');
    }
};
