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
        Schema::table('calificaciones', function (Blueprint $table) {
            // Quitar las restricciones de clave foránea primero
            $table->dropForeign(['grupo_id']);
            $table->dropForeign(['imparte_id']);

            // Luego eliminar las columnas
            $table->dropColumn(['grupo_id', 'imparte_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calificaciones', function (Blueprint $table) {
            // Agregar las columnas de vuelta
            $table->foreignId('grupo_id')->constrained();
            $table->foreignId('imparte_id')->constrained('imparte');
        });
    }
};
