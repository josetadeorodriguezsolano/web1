<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    //cambio esme
    public function up(): void
    {
        Schema::create('maestros', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('apellidos');
            $table->string('telefono',10);
            $table->string('curp',18);
            $table->string('direccion',500);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('name');
            $table->index('apellidos');
            $table->unique('curp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maestros');
    }
};
