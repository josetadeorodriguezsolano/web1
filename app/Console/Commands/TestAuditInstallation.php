<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomAudit;
use Illuminate\Support\Facades\Schema;

class TestAuditInstallation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:test-installation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica que Laravel Auditing esté instalado correctamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando instalación de Laravel Auditing...');

        // Verificar que la tabla existe
        if (!Schema::hasTable('audits')) {
            $this->error('❌ La tabla "audits" no existe. Ejecuta: php artisan migrate');
            return 1;
        }

        $this->info('✅ Tabla "audits" encontrada');

        // Verificar columnas personalizadas
        $customColumns = ['academic_year', 'grupo_id', 'materia_id', 'academic_context'];
        $missingColumns = [];

        foreach ($customColumns as $column) {
            if (!Schema::hasColumn('audits', $column)) {
                $missingColumns[] = $column;
            }
        }

        if (!empty($missingColumns)) {
            $this->warn('⚠️  Columnas personalizadas faltantes: ' . implode(', ', $missingColumns));
            $this->info('💡 Para agregarlas, ejecuta: php artisan make:migration add_custom_fields_to_audits_table --table=audits');
            $this->info('   Y usa la migración proporcionada en la documentación.');
        } else {
            $this->info('✅ Columnas personalizadas encontradas');
        }

        // Verificar que el modelo funciona
        try {
            $auditCount = CustomAudit::count();
            $this->info("✅ Modelo CustomAudit funciona correctamente. Registros actuales: $auditCount");
        } catch (\Exception $e) {
            $this->error('❌ Error en el modelo CustomAudit: ' . $e->getMessage());
            return 1;
        }

        // Verificar archivo de configuración
        if (!file_exists(config_path('audit.php'))) {
            $this->error('❌ Archivo config/audit.php no encontrado');
            return 1;
        }

        $this->info('✅ Archivo de configuración encontrado');

        $this->newLine();
        $this->info('🎉 ¡Laravel Auditing instalado correctamente!');
        $this->info('📋 Siguiente paso: Configurar modelos auditables (Tarea 3.2)');

        return 0;
    }
}
