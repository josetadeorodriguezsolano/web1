<?php


namespace App\Http\Controllers;

use App\Models\Horario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PdfController extends Controller
{
  public function horarioGrupo($grupo_id)
{
    $horarios = DB::table('horarios')
        ->join('imparte', 'horarios.imparte_id', '=', 'imparte.id')
        ->join('materias', 'imparte.materia_id', '=', 'materias.id')
        ->join('grupos', 'imparte.grupo_id', '=', 'grupos.id')
        ->join('maestros', 'imparte.maestro_id', '=', 'maestros.id')
        ->select(
            'horarios.hora_numero',
            'horarios.dia_semana',
            'grupos.letra AS grupo',
            'materias.nombre AS materia',
            DB::raw("CONCAT(maestros.name, ' ', maestros.apellidos) AS maestro")
        )
        ->where('grupos.id', $grupo_id)
        ->orderBy('horarios.hora_numero')
        ->orderBy('horarios.dia_semana')
        ->get();

    // Agrupar por hora para construir la tabla por filas
    $horas = [];
    $nombreGrupo = '';
    foreach ($horarios as $item) {
        $horas[$item->hora_numero][$item->dia_semana] = "{$item->materia} ({$item->maestro})";
        $nombreGrupo = $item->grupo;
    }

    $pdf = Pdf::loadView('horario_pdf', [
        'horas' => $horas,
        'grupo' => $nombreGrupo
    ]);

    return $pdf->stream();
    // return $pdf->download("horario_grupo_{$grupo_id}.pdf");
}

}
