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
Schema::create('horarios', function (Blueprint $table) {
    $table->id();
    $table->foreignId('imparte_id')->constrained('imparte');
    $table->unsignedTinyInteger('hora_numero');
    $table->unsignedTinyInteger('dia_semana');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
