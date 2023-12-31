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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('pharmaceutical_id');
            // $table->unsignedBigInteger('quantity');
            $table->foreignId('user_id')->constrained();
            $table->string('status')->default("in process");
            $table->string('payment')->default("unpaid");
            $table->unsignedDecimal('totale_price', 8, 2); // Adjust the precision and scale as needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
