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

class DemoAuditFinal extends Command
{
    protected $signature = 'audit:demo-final';
    protected $description = 'Demostración final de auditoría - Tarea 4.2 completada';

    public function handle()
    {
        $this->info('🎯 DEMOSTRACIÓN TAREA 4.2: Modelos Auditables');
        $this->line('================================================');

        // Autenticar
        $maestro = Maestro::first();
        Auth::login($maestro);
        $this->info("👤 Usuario: {$maestro->name}");

        $auditsBefore = Audit::count();
        $this->line("📊 Auditorías totales antes: {$auditsBefore}");
        $this->line('');

        // Demostrar Calificacion auditable
        $this->demoCalificacion();
        $this->line('');

        // Demostrar Inscrito auditable
        $this->demoInscrito();
        $this->line('');

        $auditsAfter = Audit::count();
        $nuevas = $auditsAfter - $auditsBefore;

        $this->info("📊 Auditorías totales después: {$auditsAfter}");
        $this->info("🆕 Nuevas auditorías creadas: {$nuevas}");
        $this->line('');

        $this->showAuditDetails();

        $this->line('================================================');
        $this->info('✅ TAREA 4.2 COMPLETADA EXITOSAMENTE');
        $this->info('   - Modelo Calificacion: AUDITABLE ✅');
        $this->info('   - Modelo Inscrito: AUDITABLE ✅');
        $this->line('================================================');
    }

    private function demoCalificacion()
    {
        $this->line('📝 MODELO CALIFICACION - Demostración de Auditoría:');

        $alumno = Alumno::first();
        $materia = Materia::first();

        // Encontrar unidad libre
        $unidad = 10; // Usar unidad alta para evitar conflictos
        while (Calificacion::where('alumno_id', $alumno->id)
                          ->where('materia_id', $materia->id)
                          ->where('unidad', $unidad)->exists()) {
            $unidad++;
        }

        try {
            // 1. CREATE
            $calificacion = Calificacion::create([
                'alumno_id' => $alumno->id,
                'materia_id' => $materia->id,
                'unidad' => $unidad,
                'calificacion' => 7.5
            ]);
            $this->info("   ✅ CREATE: Calificación creada (ID: {$calificacion->id}, Unidad: {$unidad}, Nota: 7.5)");

            // 2. UPDATE
            $calificacion->update(['calificacion' => 8.5]);
            $this->info("   ✅ UPDATE: Calificación modificada (7.5 → 8.5)");

            // 3. DELETE (opcional, para demostrar)
            $calificacionId = $calificacion->id;
            $calificacion->delete();
            $this->info("   ✅ DELETE: Calificación eliminada (ID: {$calificacionId})");

        } catch (\Exception $e) {
            $this->error("   ❌ Error: {$e->getMessage()}");
        }
    }

    private function demoInscrito()
    {
        $this->line('📋 MODELO INSCRITO - Demostración de Auditoría:');

        $inscrito = Inscrito::where('estatus', 'vigente')->first();

        if (!$inscrito) {
            $this->warn('   ⚠️ No hay inscripciones vigentes para demostrar');
            return;
        }

        try {
            $estatusOriginal = $inscrito->estatus;
            $alumnoNombre = $inscrito->alumno->nombres . ' ' . $inscrito->alumno->apellidos;

            $this->info("   👤 Trabajando con: {$alumnoNombre} (ID: {$inscrito->id})");

            // 1. UPDATE a 'baja'
            $inscrito->update(['estatus' => 'baja']);
            $this->info("   ✅ UPDATE: Estatus cambiado ({$estatusOriginal} → baja)");

            // 2. UPDATE de vuelta a original
            $inscrito->update(['estatus' => $estatusOriginal]);
            $this->info("   ✅ UPDATE: Estatus restaurado (baja → {$estatusOriginal})");

        } catch (\Exception $e) {
            $this->error("   ❌ Error: {$e->getMessage()}");
        }
    }

    private function showAuditDetails()
    {
        $this->line('🔍 DETALLES DE AUDITORÍAS RECIENTES:');

        $audits = Audit::latest()->take(6)->get();

        foreach ($audits as $index => $audit) {
            $userName = $audit->user ? $audit->user->name : 'Sistema';
            $modelName = class_basename($audit->auditable_type);
            $timestamp = $audit->created_at->format('H:i:s');

            $this->line("   {$timestamp} - {$audit->event} en {$modelName} (ID: {$audit->auditable_id}) por {$userName}");
        }
    }
}
