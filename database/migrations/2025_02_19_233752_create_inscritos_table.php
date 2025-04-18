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
        Schema::create('inscritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained();
            $table->foreignId('alumno_id')->constrained();
            $table->enum('estatus', ['vigente', 'baja', 'egresado'])->default('vigente'); // Columna 'estatus' con valores restringidos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscritos');
    }
};
