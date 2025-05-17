<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorariosTable extends Migration
{
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('hora_numero'); // 1, 2, 3...
            $table->string('dia_semana'); // Lunes, Martes...

            // Relación con la tabla "imparte"
            $table->unsignedBigInteger('imparte_id');
            $table->foreign('imparte_id')->references('id')->on('imparte')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('horarios');
    }
}
