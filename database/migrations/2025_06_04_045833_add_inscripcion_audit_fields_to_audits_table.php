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
            if (!Schema::hasColumn('audits', 'inscripcion_context')) {
                $table->text('inscripcion_context')->nullable()->comment('Contexto específico de cambios de inscripción');
            }

            if (!Schema::hasColumn('audits', 'estatus_anterior')) {
                $table->string('estatus_anterior')->nullable()->comment('Estatus anterior de la inscripción');
            }

            if (!Schema::hasColumn('audits', 'estatus_nuevo')) {
                $table->string('estatus_nuevo')->nullable()->comment('Nuevo estatus de la inscripción');
            }

            if (!Schema::hasColumn('audits', 'alumno_matricula')) {
                $table->string('alumno_matricula')->nullable()->comment('Matrícula del alumno afectado');
            }

            if (!Schema::hasColumn('audits', 'es_cambio_critico')) {
                $table->boolean('es_cambio_critico')->default(false)->comment('Indica si es un cambio crítico que requiere atención');
            }

            if (!Schema::hasColumn('audits', 'motivo_cambio')) {
                $table->text('motivo_cambio')->nullable()->comment('Motivo del cambio de estatus');
            }
        });

        // Agregar índices para optimizar consultas de reportes de inscripciones
        Schema::table('audits', function (Blueprint $table) {
            try {
                $table->index(['auditable_type', 'estatus_anterior', 'estatus_nuevo'], 'audit_inscripcion_cambios');
            } catch (\Exception $e) {
                // El índice ya existe, continuar
            }

            try {
                $table->index(['alumno_matricula', 'created_at'], 'audit_alumno_fecha');
            } catch (\Exception $e) {
                // El índice ya existe, continuar
            }

            try {
                $table->index(['es_cambio_critico', 'created_at'], 'audit_cambios_criticos');
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
            // Eliminar índices primero
            try {
                $table->dropIndex('audit_inscripcion_cambios');
                $table->dropIndex('audit_alumno_fecha');
                $table->dropIndex('audit_cambios_criticos');
            } catch (\Exception $e) {
                // Los índices no existen, continuar
            }

            // Eliminar columnas específicas de inscripciones
            $columnasAEliminar = [
                'inscripcion_context',
                'estatus_anterior',
                'estatus_nuevo',
                'alumno_matricula',
                'es_cambio_critico',
                'motivo_cambio'
            ];

            foreach ($columnasAEliminar as $columna) {
                if (Schema::hasColumn('audits', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
