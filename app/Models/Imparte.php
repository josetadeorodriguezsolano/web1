<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Grupo;
use App\Models\Materia;

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
        return Calificacion::query()
            ->where('materia_id', $this->materia_id)
            ->whereHas('alumno.inscritos', function ($query) {
                $query->where('grupo_id', $this->grupo_id)
                      ->where('estatus', 'vigente');
            });
    }
}
