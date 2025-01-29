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
        Schema::create('material_ordenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('ordene_id');
            $table->unsignedBigInteger('serializado_id')->nullable();
            $table->float('cantidad');

            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials');
            $table->foreign('ordene_id')->references('id')->on('ordenes');
            $table->foreign('serializado_id')->references('id')->on('serializados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_ordenes');
    }
};
