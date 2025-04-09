<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function lista($grupo_id){
        $grupo = Grupo::find($grupo_id);
        //return view('lista',["alumnos"=>$grupo->alumnos]);
        $pdf = Pdf::loadView('lista', ["alumnos"=>$grupo->alumnos]);
        return $pdf->stream();
        //return $pdf->download('lista.pdf');
    }
}
