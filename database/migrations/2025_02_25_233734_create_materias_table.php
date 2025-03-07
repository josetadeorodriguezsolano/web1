<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('clave')->unique();
            $table->integer('creditos');
            $table->enum('grado',['1','2','3'])->default('1');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('materias');
    }
};
