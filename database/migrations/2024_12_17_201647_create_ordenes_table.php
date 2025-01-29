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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->string('acta',15);
            $table->string('ticket', 16);
            $table->string('manga',10);
            $table->text('observaciones');
            $table->unsignedBigInteger('precio_id');
            $table->unsignedBigInteger('persona_id');
            $table->unsignedBigInteger('actividad_id');
            $table->unsignedBigInteger('ciudad_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('abonado_id');
            $table->timestamps();

            $table->foreign('precio_id')->references('id')->on('precios');
            $table->foreign('persona_id')->references('id')->on('personas');
            $table->foreign('actividad_id')->references('id')->on('actividads');
            $table->foreign('ciudad_id')->references('id')->on('ciudads');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('abonado_id')->references('id')->on('abonados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
