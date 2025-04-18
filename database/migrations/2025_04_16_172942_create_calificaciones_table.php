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
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('imparte_id')->constrained('imparte');
            $table->string('unidad'); // Campo para la unidad
            $table->float('calificacion', 3, 1)->check('calificacion >= 1.0 AND calificacion <= 10.0');
            $table->timestamps();

            $table->unique(['alumno_id', 'imparte_id', 'unidad']);
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
