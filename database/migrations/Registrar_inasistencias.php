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
        Schema::create('inacistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maestros_id')->constrained()->onDelete('cascade');
            $table->foreignId('materias_id')->constrained()->onDelete('cascade');
            $table->foreignId('grupos_id')->constrained()->onDelete('cascade');
            $table->dateTime('horario_falta');
            $table->dateTime('horario_llegada')->nullable();
            $table->string('justificacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inacistencias');
    }
};
