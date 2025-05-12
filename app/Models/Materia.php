<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Materia extends Model {
    use HasFactory;

    protected $table = 'materias';

    protected $fillable = ['nombre', 'clave', 'creditos'];

  public function materia()
{
    return $this->belongsTo(Imparte::class)->belongsTo(Materia::class, 'imparte_id', 'id');
}

}
