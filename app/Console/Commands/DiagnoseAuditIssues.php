<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Calificacion;
use App\Models\Inscrito;
use App\Models\CustomAudit;
use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Maestro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class DiagnoseAuditIssues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:diagnose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnostica problemas con el sistema de auditorías';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Diagnosticando sistema de auditorías...');

        // 1. Verificar configuración
        $this->checkConfiguration();

        // 2. Verificar modelos
        $this->checkModels();

        // 3. Verificar base de datos
        $this->checkDatabase();

        // 4. Probar auditoría paso a paso
        $this->testAuditStepByStep();

        return 0;
    }

    private function checkConfiguration()
    {
        $this->info('📋 1. Verificando configuración...');

        // Verificar archivo de configuración
        $auditConfig = config('audit');
        if (!$auditConfig) {
            $this->error('❌ Archivo config/audit.php no encontrado');
            return;
        }

        $this->line("  ✓ Configuración cargada");
        $this->line("  - Implementation: " . $auditConfig['implementation']);
        $this->line("  - Driver: " . $auditConfig['driver']);
        $this->line("  - Eventos: " . implode(', ', $auditConfig['events']));
        $this->line("  - Strict mode: " . ($auditConfig['strict'] ? 'Sí' : 'No'));
    }

    private function checkModels()
    {
        $this->info('🎯 2. Verificando modelos...');

        // Verificar Calificacion
        $cal = new Calificacion();
        $this->line("  Calificacion:");
        $this->line("    - Implementa Auditable: " . ($cal instanceof Auditable ? '✓' : '❌'));
        $this->line("    - Eventos configurados: " . implode(', ', $cal->getAuditEvents()));
        $this->line("    - Strict mode: " . ($cal->getAuditStrict() ? 'Sí' : 'No'));
        $this->line("    - Timestamps: " . ($cal->getAuditTimestamps() ? 'Sí' : 'No'));

        // Verificar Inscrito
        $ins = new Inscrito();
        $this->line("  Inscrito:");
        $this->line("    - Implementa Auditable: " . ($ins instanceof Auditable ? '✓' : '❌'));
        $this->line("    - Eventos configurados: " . implode(', ', $ins->getAuditEvents()));
        $this->line("    - Strict mode: " . ($ins->getAuditStrict() ? 'Sí' : 'No'));
        $this->line("    - Timestamps: " . ($ins->getAuditTimestamps() ? 'Sí' : 'No'));
    }

    private function checkDatabase()
    {
        $this->info('🗃️  3. Verificando base de datos...');

        try {
            $auditCount = DB::table('audits')->count();
            $this->line("  ✓ Tabla 'audits' accesible");
            $this->line("  - Registros actuales: $auditCount");

            // Verificar estructura
            $columns = DB::select("DESCRIBE audits");
            $columnNames = collect($columns)->pluck('Field')->toArray();

            $requiredColumns = ['academic_year', 'grupo_id', 'materia_id', 'academic_context'];
            foreach ($requiredColumns as $col) {
                if (in_array($col, $columnNames)) {
                    $this->line("  ✓ Columna '$col'");
                } else {
                    $this->error("  ❌ Columna '$col' faltante");
                }
            }
        } catch (\Exception $e) {
            $this->error("❌ Error accediendo a la tabla audits: " . $e->getMessage());
        }
    }

    private function testAuditStepByStep()
    {
        $this->info('🧪 4. Probando auditoría paso a paso...');

        try {
            // Autenticar
            $maestro = Maestro::first();
            if ($maestro) {
                Auth::login($maestro);
                $this->line("  ✓ Autenticado como: {$maestro->name}");
            }

            // Obtener datos para prueba
            $alumno = Alumno::first();
            $materia = Materia::first();

            if (!$alumno || !$materia) {
                $this->error("  ❌ No hay datos para probar");
                return;
            }

            $this->line("  ℹ️  Usando Alumno ID: {$alumno->id}, Materia ID: {$materia->id}");

            // Contar antes
            $before = CustomAudit::count();
            $this->line("  📊 Auditorías antes: $before");

            // Habilitar logging para debug
            DB::enableQueryLog();

            // Crear calificación con eventos explícitos
            $this->line("  🔨 Creando calificación...");

            $calificacion = new Calificacion([
                'alumno_id' => $alumno->id,
                'materia_id' => $materia->id,
                'unidad' => 4,
                'calificacion' => 8.5
            ]);

            // Verificar que el modelo tiene auditoría habilitada
            $this->line("  - Auditoría habilitada: " . ($calificacion->isAuditingEnabled() ? 'Sí' : 'No'));

            // Guardar
            $saved = $calificacion->save();
            $this->line("  - Guardado: " . ($saved ? 'Sí' : 'No'));
            $this->line("  - ID creado: " . $calificacion->id);

            // Verificar queries ejecutadas
            $queries = DB::getQueryLog();
            $auditQueries = collect($queries)->filter(function($query) {
                return str_contains($query['query'], 'audits');
            });

            $this->line("  - Queries de auditoría ejecutadas: " . $auditQueries->count());

            foreach ($auditQueries as $query) {
                $this->line("    * " . $query['query']);
            }

            // Contar después
            $after = CustomAudit::count();
            $this->line("  📊 Auditorías después: $after");
            $this->line("  🆕 Nuevas auditorías: " . ($after - $before));

            // Limpiar
            $calificacion->delete();

        } catch (\Exception $e) {
            $this->error("❌ Error en prueba: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
