<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('imparte', function (Blueprint $table) {
            $table->enum('dia', ['lunes', 'martes', 'miércoles', 'jueves', 'viernes'])->after('maestro_id');
            $table->time('hora_inicio')->after('dia');
            $table->time('hora_fin')->after('hora_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('imparte', function (Blueprint $table) {
            $table->dropColumn(['dia', 'hora_inicio', 'hora_fin']);
        });
    }
};
