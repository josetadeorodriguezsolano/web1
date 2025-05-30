<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'imparte_id',
        'dia',
        'hora_id',
    ];

    public function imparte()
    {
        return $this->belongsTo(Imparte::class);
    }

    public function hora()
    {
        return $this->belongsTo(Hora::class);
    }
}