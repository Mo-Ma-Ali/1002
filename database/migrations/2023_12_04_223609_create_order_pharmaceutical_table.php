<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('order_pharmaceutical', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained();
        $table->foreignId('pharmaceutical_id')->constrained();
        $table->foreignId('number')->constrained();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_pharmaceutical');
    }
};
