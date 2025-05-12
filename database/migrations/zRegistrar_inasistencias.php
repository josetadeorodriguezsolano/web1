<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('inasistencias', function (Blueprint $table) {
            $table->id();

            // Llave foránea a la tabla imparte
          $table->foreignId('imparte_id')->constrained('imparte');

            // Llave foránea a la tabla horarios
            $table->foreignId('horario_id')->constrained('horarios');

            // Campo de justificación (opcional)
            $table->string('justificacion')->nullable();

            // Campo de fecha (solo día, mes y año)
            $table->date('fecha')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('inasistencias');
    }
};
