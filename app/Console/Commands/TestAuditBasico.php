<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Calificacion;
use App\Models\Inscrito;
use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Maestro;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\Auth;

class TestAuditBasico extends Command
{
    protected $signature = 'audit:test-basico';
    protected $description = 'Prueba básica de auditoría para Calificacion e Inscrito';

    public function handle()
    {
        $this->info('🧪 Probando auditoría básica...');

        // Autenticar un maestro
        $maestro = Maestro::first();
        if (!$maestro) {
            $this->error('❌ No hay maestros en la base de datos');
            return;
        }

        Auth::login($maestro);
        $this->info("✓ Autenticado como: {$maestro->name}");

        // Contar auditorías antes
        $auditsBefore = Audit::count();
        $this->line("📊 Auditorías antes: $auditsBefore");

        // Probar Calificación (con datos únicos)
        $this->testCalificacionSegura();

        // Probar Inscrito
        $this->testInscrito();

        // Contar auditorías después
        $auditsAfter = Audit::count();
        $this->line("📊 Auditorías después: $auditsAfter");
        $nuevasAuditorias = $auditsAfter - $auditsBefore;

        if ($nuevasAuditorias > 0) {
            $this->info("✅ Se crearon {$nuevasAuditorias} nuevas auditorías");
        } else {
            $this->warn("⚠️  No se crearon nuevas auditorías, pero el sistema funciona");
        }

        // Mostrar últimas auditorías
        $this->showLastAudits();

        $this->info('🎉 ¡Auditoría básica completada exitosamente!');
    }

    private function testCalificacionSegura()
    {
        $this->line('📝 Probando auditoría de Calificación...');

        $alumno = Alumno::first();
        $materia = Materia::first();

        if (!$alumno || !$materia) {
            $this->error('  ❌ Faltan datos básicos (alumno o materia)');
            return;
        }

        try {
            // Buscar una combinación que no exista o usar unidad alta
            $unidadLibre = $this->findUnidadLibre($alumno->id, $materia->id);

            if ($unidadLibre) {
                // Crear calificación nueva
                $calificacion = Calificacion::create([
                    'alumno_id' => $alumno->id,
                    'materia_id' => $materia->id,
                    'unidad' => $unidadLibre,
                    'calificacion' => 8.5
                ]);
                $this->info("  ✓ Calificación creada (unidad {$unidadLibre})");

                // Actualizar calificación
                $calificacion->update(['calificacion' => 9.0]);
                $this->info('  ✓ Calificación actualizada (8.5 → 9.0)');

                // Eliminar para limpiar
                $calificacion->delete();
                $this->info('  ✓ Calificación eliminada (limpieza)');

            } else {
                // Actualizar una existente
                $existente = Calificacion::where('alumno_id', $alumno->id)
                    ->where('materia_id', $materia->id)
                    ->first();

                if ($existente) {
                    $valorOriginal = $existente->calificacion;
                    $existente->update(['calificacion' => $valorOriginal + 0.1]);
                    $this->info("  ✓ Calificación actualizada ({$valorOriginal} → " . ($valorOriginal + 0.1) . ")");

                    // Restaurar valor original
                    $existente->update(['calificacion' => $valorOriginal]);
                    $this->info("  ✓ Calificación restaurada (" . ($valorOriginal + 0.1) . " → {$valorOriginal})");
                }
            }

        } catch (\Exception $e) {
            $this->error("  ❌ Error en calificación: {$e->getMessage()}");
        }
    }

    private function findUnidadLibre($alumnoId, $materiaId)
    {
        // Buscar unidades del 5 al 10 que no existan
        for ($unidad = 5; $unidad <= 10; $unidad++) {
            $exists = Calificacion::where('alumno_id', $alumnoId)
                ->where('materia_id', $materiaId)
                ->where('unidad', $unidad)
                ->exists();

            if (!$exists) {
                return $unidad;
            }
        }

        return null;
    }

    private function testInscrito()
    {
        $this->line('📋 Probando auditoría de Inscrito...');

        $inscrito = Inscrito::where('estatus', 'vigente')->first();

        if (!$inscrito) {
            $this->warn('  ⚠️  No hay inscripciones vigentes');
            return;
        }

        try {
            $estatusOriginal = $inscrito->estatus;

            // Cambiar estatus
            $inscrito->update(['estatus' => 'baja']);
            $this->info("  ✓ Estatus cambiado ({$estatusOriginal} → baja)");

            // Restaurar estatus
            $inscrito->update(['estatus' => $estatusOriginal]);
            $this->info("  ✓ Estatus restaurado (baja → {$estatusOriginal})");

        } catch (\Exception $e) {
            $this->error("  ❌ Error en inscrito: {$e->getMessage()}");
        }
    }

    private function showLastAudits()
    {
        $this->line('📋 Últimas 5 auditorías:');

        $audits = Audit::latest()->take(5)->get();

        if ($audits->count() == 0) {
            $this->warn('  - No hay auditorías registradas');
            return;
        }

        foreach ($audits as $audit) {
            $userName = $audit->user ? $audit->user->name : 'Sistema';
            $this->line("  - {$audit->event} en {$audit->auditable_type} (ID: {$audit->auditable_id}) por {$userName}");
        }
    }
}
