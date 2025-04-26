<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Imparte extends Model
{
    use HasFactory;
    public $table = "imparte";
    public $timestamps = true;

    protected $fillable = [
        'materia_id',
        'maestro_id',
        'grupo_id',
    ];

    public function grupo(){
        return $this->belongsTo(Grupo::class);
    }

    public function materia(){
        return $this->belongsTo(Materia::class);
    }

    public function maestro(){
        return $this->belongsTo(Maestro::class);
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class, 'imparte_id'); // Especificar la clave foránea si no sigue la convención
    }
}
