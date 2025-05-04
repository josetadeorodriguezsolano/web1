<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Imparte extends Model
{
    use HasFactory;

    protected $fillable = [
        'grupo_id',
        'materia_id',
        'maestro_id',
        'dia',
        'hora_inicio',
        'hora_fin',
    ];

    public $table = "imparte";
    public $timestamps = true;

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function maestro()
    {
        return $this->belongsTo(Maestro::class);
    }
}
