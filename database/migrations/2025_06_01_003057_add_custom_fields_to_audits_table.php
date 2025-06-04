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
        Schema::table('audits', function (Blueprint $table) {
            // Verificar si las columnas ya existen antes de agregarlas
            if (!Schema::hasColumn('audits', 'academic_year')) {
                $table->integer('academic_year')->nullable()->comment('Año académico del cambio');
            }

            if (!Schema::hasColumn('audits', 'grupo_id')) {
                $table->unsignedBigInteger('grupo_id')->nullable()->comment('ID del grupo relacionado');
            }

            if (!Schema::hasColumn('audits', 'materia_id')) {
                $table->unsignedBigInteger('materia_id')->nullable()->comment('ID de la materia relacionada');
            }

            if (!Schema::hasColumn('audits', 'academic_context')) {
                $table->string('academic_context')->nullable()->comment('Contexto académico adicional');
            }
        });

        // Agregar índices si no existen
        Schema::table('audits', function (Blueprint $table) {
            try {
                $table->index(['academic_year', 'grupo_id'], 'audits_academic_year_grupo_id_index');
            } catch (\Exception $e) {
                // El índice ya existe, continuar
            }

            try {
                $table->index(['materia_id'], 'audits_materia_id_index');
            } catch (\Exception $e) {
                // El índice ya existe, continuar
            }

            try {
                $table->index(['auditable_type', 'auditable_id', 'materia_id', 'created_at'], 'audit_alumno_materia_fecha');
            } catch (\Exception $e) {
                // El índice ya existe, continuar
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            // Eliminar columnas personalizadas
            $table->dropColumn(['academic_year', 'grupo_id', 'materia_id', 'academic_context']);

            // Eliminar índices
            try {
                $table->dropIndex('audits_academic_year_grupo_id_index');
                $table->dropIndex('audits_materia_id_index');
                $table->dropIndex('audit_alumno_materia_fecha');
            } catch (\Exception $e) {
                // Los índices no existen, continuar
            }
        });
    }
};
