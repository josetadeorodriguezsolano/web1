<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inscrito extends Model
{
    use HasFactory;

    public function grupo(){
        return $this->belongsTo(Grupo::class);//,'id','grupo_id');
        //return $this->hasOne();// 1 a 1
        //return $this->hasMany();// 1 a muchos
        //return $this->belongsTo();// inverso de hasOne y hasMany
    }

    public function alumno(){
        return $this->belongsTo(Alumno::class);//,'id','alumno_id');
    }
}
