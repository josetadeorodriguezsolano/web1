<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('imparte_id');
            $table->unsignedBigInteger('hora_id'); // antes era 'hora'
            $table->enum('dia', ['lunes', 'martes', 'miércoles', 'jueves', 'viernes']);
            $table->timestamps();

            $table->foreign('imparte_id')->references('id')->on('imparte')->onDelete('cascade');
            $table->foreign('hora_id')->references('id')->on('horas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
