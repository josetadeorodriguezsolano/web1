<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Horario;
use App\Models\Imparte; // Asegúrate de que este modelo esté importado

class HorarioController extends Controller
{
    // Mostrar todos los horarios
    public function index()
    {
        // Obtiene todos los horarios de la base de datos
        $horarios = Horario::all();
        return view('horarios.index', compact('horarios')); // Pasa los horarios a la vista
    }

    // Guardar un nuevo horario
    public function store(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'imparte_id' => 'required|exists:imparte,id', // Validar que el imparte_id exista en la tabla 'imparte'
            'hora_numero' => 'required|integer|between:1,7', // Validar que la hora esté entre 1 y 7
            'dia_semana' => 'required|integer|between:1,5', // Validar que el día de la semana esté entre 1 y 5
        ]);

        // Crear un nuevo horario en la base de datos
        Horario::create([
            'imparte_id' => $request->imparte_id,
            'hora_numero' => $request->hora_numero,
            'dia_semana' => $request->dia_semana,
        ]);

        return redirect()->back()->with('success', 'Horario creado correctamente.'); // Redirige y muestra un mensaje de éxito
    }

    // Eliminar un horario
    public function destroy($id)
    {
        // Encuentra el horario por ID
        $horario = Horario::findOrFail($id);
        // Elimina el horario
        $horario->delete();

        return redirect()->back()->with('success', 'Horario eliminado correctamente.'); // Redirige y muestra un mensaje de éxito
    }

    // Obtener los horarios por maestro
    public function horariosPorMaestro($id_maestro)
    {
        // Obtiene los horarios que corresponden a un maestro específico
        $horarios = Horario::whereHas('imparte', function ($query) use ($id_maestro) {
            $query->where('maestro_id', $id_maestro); // Filtra los horarios por el ID del maestro
        })->get();

        // Retorna los horarios en formato JSON
        return response()->json($horarios);
    }
}
