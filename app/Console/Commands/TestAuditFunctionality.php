<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Calificacion;
use App\Models\Inscrito;
use App\Models\CustomAudit;
use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Maestro;
use Illuminate\Support\Facades\Auth;

class TestAuditFunctionality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:test-functionality';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la funcionalidad de auditoría en modelos configurados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Probando funcionalidad de auditorías...');

        try {
            // Obtener un maestro para simular autenticación
            $maestro = Maestro::first();
            if (!$maestro) {
                $this->error('❌ No hay maestros en la base de datos para probar');
                return 1;
            }

            // Simular autenticación
            Auth::login($maestro);
            $this->info("👤 Autenticado como: {$maestro->name}");

            // Contar auditorías antes
            $auditsBefore = CustomAudit::count();
            $this->info("📊 Auditorías antes de las pruebas: $auditsBefore");

            // Probar auditoría de calificaciones
            $this->testCalificacionAudit();

            // Probar auditoría de inscripciones
            $this->testInscripcionAudit();

            // Contar auditorías después
            $auditsAfter = CustomAudit::count();
            $newAudits = $auditsAfter - $auditsBefore;

            $this->info("📊 Auditorías después de las pruebas: $auditsAfter");
            $this->info("🆕 Nuevas auditorías creadas: $newAudits");

            if ($newAudits > 0) {
                $this->info('✅ ¡Auditorías funcionando correctamente!');
                $this->showLatestAudits();
            } else {
                $this->warn('⚠️  No se crearon nuevas auditorías. Verificar configuración.');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error durante las pruebas: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Probar auditoría de calificaciones
     */
    private function testCalificacionAudit()
    {
        $this->info('🎯 Probando auditoría de calificaciones...');

        // Buscar una calificación existente o crear una
        $alumno = Alumno::first();
        $materia = Materia::first();

        if (!$alumno || !$materia) {
            $this->warn('⚠️  No hay alumnos o materias para probar calificaciones');
            return;
        }

        // Buscar una combinación única para prueba
        $unidadPrueba = 4; // Usar unidad 4 que es menos probable que exista

        // Verificar si ya existe y eliminarla si es necesario
        $existente = Calificacion::where([
            'alumno_id' => $alumno->id,
            'materia_id' => $materia->id,
            'unidad' => $unidadPrueba
        ])->first();

        if ($existente) {
            $existente->delete();
            $this->line("  ℹ️  Calificación existente eliminada para prueba");
        }

        try {
            // Crear una calificación
            $calificacion = Calificacion::create([
                'alumno_id' => $alumno->id,
                'materia_id' => $materia->id,
                'unidad' => $unidadPrueba,
                'calificacion' => 8.5
            ]);

            $this->line("  ✓ Calificación creada (ID: {$calificacion->id})");

            // Actualizar la calificación
            $calificacion->update(['calificacion' => 9.0]);
            $this->line("  ✓ Calificación actualizada (8.5 → 9.0)");

            // Actualizar nuevamente
            $calificacion->update(['calificacion' => 7.5]);
            $this->line("  ✓ Calificación actualizada (9.0 → 7.5)");

            // Eliminar la calificación de prueba
            $calificacion->delete();
            $this->line("  ✓ Calificación eliminada");

        } catch (\Exception $e) {
            $this->error("  ❌ Error en prueba de calificación: " . $e->getMessage());

            // Intentar con otra combinación
            $this->tryAlternativeCalificacionTest($alumno, $materia);
        }
    }

    /**
     * Intentar prueba alternativa de calificaciones
     */
    private function tryAlternativeCalificacionTest($alumno, $materia)
    {
        $this->line("  🔄 Intentando con calificación existente...");

        // Buscar una calificación existente para actualizar
        $calificacionExistente = Calificacion::where('alumno_id', $alumno->id)->first();

        if ($calificacionExistente) {
            $valorOriginal = $calificacionExistente->calificacion;
            $nuevoValor = $valorOriginal == 10.0 ? 9.5 : $valorOriginal + 0.5;

            // Actualizar calificación existente
            $calificacionExistente->update(['calificacion' => $nuevoValor]);
            $this->line("  ✓ Calificación existente actualizada ({$valorOriginal} → {$nuevoValor})");

            // Restaurar valor original
            $calificacionExistente->update(['calificacion' => $valorOriginal]);
            $this->line("  ✓ Calificación restaurada a valor original ({$nuevoValor} → {$valorOriginal})");
        } else {
            $this->warn("  ⚠️  No se pudo probar calificaciones - no hay registros disponibles");
        }
    }

    /**
     * Probar auditoría de inscripciones
     */
    private function testInscripcionAudit()
    {
        $this->info('📝 Probando auditoría de inscripciones...');

        $alumno = Alumno::first();
        $grupo = Grupo::first();

        if (!$alumno || !$grupo) {
            $this->warn('⚠️  No hay alumnos o grupos para probar inscripciones');
            return;
        }

        // Verificar si ya existe una inscripción para este alumno
        $inscripcionExistente = Inscrito::where('alumno_id', $alumno->id)->first();

        if ($inscripcionExistente) {
            $this->line("  ℹ️  Usando inscripción existente (ID: {$inscripcionExistente->id})");

            $estatusOriginal = $inscripcionExistente->estatus;

            // Cambiar a un estatus diferente
            $nuevoEstatus = $estatusOriginal === 'vigente' ? 'baja' : 'vigente';
            $inscripcionExistente->update(['estatus' => $nuevoEstatus]);
            $this->line("  ✓ Estatus actualizado ({$estatusOriginal} → {$nuevoEstatus})");

            // Restaurar estatus original
            $inscripcionExistente->update(['estatus' => $estatusOriginal]);
            $this->line("  ✓ Estatus restaurado ({$nuevoEstatus} → {$estatusOriginal})");

        } else {
            try {
                // Crear nueva inscripción
                $inscripcion = Inscrito::create([
                    'alumno_id' => $alumno->id,
                    'grupo_id' => $grupo->id,
                    'estatus' => 'vigente'
                ]);
                $this->line("  ✓ Inscripción creada (ID: {$inscripcion->id})");

                // Cambiar estatus
                $inscripcion->update(['estatus' => 'baja']);
                $this->line("  ✓ Estatus cambiado (vigente → baja)");

                // Cambiar a egresado
                $inscripcion->update(['estatus' => 'egresado']);
                $this->line("  ✓ Estatus cambiado (baja → egresado)");

                // Eliminar inscripción de prueba
                $inscripcion->delete();
                $this->line("  ✓ Inscripción eliminada");

            } catch (\Exception $e) {
                $this->error("  ❌ Error en prueba de inscripción: " . $e->getMessage());
            }
        }
    }

    /**
     * Mostrar las últimas auditorías creadas
     */
    private function showLatestAudits()
    {
        $this->info('📋 Últimas auditorías creadas:');

        $latestAudits = CustomAudit::with(['grupo', 'materia'])
                                  ->orderBy('created_at', 'desc')
                                  ->limit(5)
                                  ->get();

        foreach ($latestAudits as $audit) {
            $user = $audit->user ? $audit->user->name : 'Sistema';
            $event = $audit->event_description;
            $context = $audit->academic_context ?? 'Sin contexto';

            $this->line("  • {$audit->auditable_type} - {$event} por {$user}");
            $this->line("    Contexto: {$context}");
            $this->line("    Fecha: {$audit->created_at}");
            $this->line("");
        }
    }
}
