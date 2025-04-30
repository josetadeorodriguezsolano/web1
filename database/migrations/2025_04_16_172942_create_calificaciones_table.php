<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ACTUALIZAR ESTA MIGRACION EN BASE A LO USADO EN EL MODELO: CALIFICACION
     */
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alumno_id')->constrained();
            $table->foreignId('materia_id')->constrained();
            $table->foreignId('grupo_id')->constrained();
            $table->foreignId('imparte_id')->constrained('imparte');
            $table->string('unidad');
            $table->decimal('calificacion', 3, 1);
            $table->timestamps();
            $table->unique(['alumno_id', 'materia_id', 'imparte_id', 'unidad']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};

