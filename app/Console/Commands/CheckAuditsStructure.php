<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckAuditsStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:check-structure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica la estructura actual de la tabla audits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando estructura de la tabla "audits"...');

        if (!Schema::hasTable('audits')) {
            $this->error('❌ La tabla "audits" no existe');
            return 1;
        }

        // Obtener todas las columnas
        $columns = Schema::getColumnListing('audits');

        $this->info('📋 Columnas existentes:');
        foreach ($columns as $column) {
            $this->line("  - $column");
        }

        // Verificar columnas requeridas
        $requiredColumns = [
            'id', 'user_type', 'user_id', 'event', 'auditable_type', 'auditable_id',
            'old_values', 'new_values', 'url', 'ip_address', 'user_agent', 'tags',
            'created_at', 'updated_at'
        ];

        $customColumns = [
            'academic_year', 'grupo_id', 'materia_id', 'academic_context'
        ];

        $this->newLine();
        $this->info('✅ Verificando columnas básicas:');
        foreach ($requiredColumns as $column) {
            if (in_array($column, $columns)) {
                $this->line("  ✓ $column");
            } else {
                $this->error("  ❌ $column (FALTANTE)");
            }
        }

        $this->newLine();
        $this->info('🎯 Verificando columnas personalizadas:');
        foreach ($customColumns as $column) {
            if (in_array($column, $columns)) {
                $this->line("  ✓ $column");
            } else {
                $this->error("  ❌ $column (FALTANTE - necesita migración)");
            }
        }

        // Verificar índices
        $this->newLine();
        $this->info('📊 Verificando índices:');
        try {
            $indexes = DB::select("SHOW INDEX FROM audits");
            $indexNames = collect($indexes)->pluck('Key_name')->unique()->values();

            foreach ($indexNames as $indexName) {
                $this->line("  - $indexName");
            }
        } catch (\Exception $e) {
            $this->warn('No se pudieron verificar los índices: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('📈 Estadísticas:');
        try {
            $count = DB::table('audits')->count();
            $this->line("  Total de registros: $count");
        } catch (\Exception $e) {
            $this->error('No se pudo contar los registros: ' . $e->getMessage());
        }

        return 0;
    }
}
