<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imparte extends Model
{
    protected $table = 'imparte';
    use HasFactory;

    protected $fillable = [
        'materia_id',
        'grupo_id',
        'maestro_id'
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function maestro()
    {
        return $this->belongsTo(Maestro::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}