<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

class falta extends Model
{
    use HasFactory; 

    protected $table = "faltas"; 
    public $timestamps = true; 

    protected $primaryKey = "id";

    
    protected $fillable = [
        'grado_id',
        'maestro_id',
        'fecha',
        'hora',
        'justificacion'

    ];
    
}
