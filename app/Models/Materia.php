<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Materia extends Model {
    use HasFactory;

    protected $table = 'materias';

    protected $fillable = [
        'nombre',
        'clave',
        'creditos',
        'grado',
    ];

    // Scope para buscar por grado
    public function scopePorGrado($query, $grado)
    {
        return $query->where('grado', $grado);
    }

    public function impartes(): HasMany
    {
        return $this->hasMany(Imparte::class);
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

}
