<?php


namespace App\Http\Controllers;

use App\Models\Inasistencia;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFInasistenciasController extends Controller
{
    public function generar()
    {
        $inasistencias = Inasistencia::with(['maestro', 'materia', 'grupo'])->get();

        $pdf = Pdf::loadView('gpdfInasistencia', ['inasistencias' => $inasistencias]);
        return $pdf->stream('gpdfInasistencia.pdf');
    }
}
