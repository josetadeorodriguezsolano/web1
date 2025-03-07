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
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('matricula',10);
            $table->string('nombres'); //por default no se permite NULL
            $table->string('apellidos');
            $table->enum('estatus', ['vigente', 'egresado', 'baja'])->default('vigente');
            $table->string('curp', 18);
            $table->string('contacto', 20);
            $table->string('tutor');            //TUTOR
            $table->timestamps();

            $table->index('apellidos');
            $table->unique('curp');//no permitir repetidos
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
