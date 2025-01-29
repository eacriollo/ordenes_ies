<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('serializados', function (Blueprint $table) {
            $table->id();
            $table->string('serie');
            $table->datetime('fecha');
            $table->enum('estado',
                ['Disponible',
                    'Vendido',
                    'Instalado',
                    'Dañado',
                    'Devuelto',
                    'Retirado']);
            $table->unsignedBigInteger('material_id');
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serializados');
    }
};
