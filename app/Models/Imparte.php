<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Imparte extends Model
{
    use HasFactory;
    public $table = "imparte";
    public $timestamps = true;

    public function grupo(){
        return $this->belongsTo(Grupo::class);
    }

    public function materia(){
        return $this->belongsTo(Materia::class);
    }

    public function maestro(){
        return $this->belongsTo(Maestro::class);
    }
}
