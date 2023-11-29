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
        Schema::create('pharmaceuticals', function (Blueprint $table) {
            $table->id();
            $table->string('scientific_name');
            $table->string('commercial_name');
            $table->string('calssification');
            $table->string('manufacture_company');
            $table->integer('quantity_available');
            $table->date('expire_date');
            $table->float('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmaceuticals');
    }

    
};
