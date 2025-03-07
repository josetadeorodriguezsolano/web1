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
        Schema::create('imparte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_id')->constrained();
            $table->foreignId('maestro_id')->constrained();
            $table->foreignId('grupo_id')->constrained();
            $table->timestamps();

            $table->unique(['grupo_id','materia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imparte');
    }
};
