<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Imparte extends Model
{

    use HasFactory;
    protected $fillable = ['grupo_id', 'materia_id', 'maestro_id'];
    public $table = "imparte";
    public $timestamps = true;

    public function grupo(){
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(\App\Models\Materia::class);
    }
    
    public function maestro(){
        return $this->belongsTo(Maestro::class);
    }
}
