<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Maestro;
use App\Models\Imparte;
use App\Models\Inscrito;
use App\Models\Horario;
use App\Models\Hora;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;
class ReportesGrupo extends Component
{
    // Variables de filtrado
    public $grupo_id = null;
    public $generacion = null;
    public $grado = null;
    public $letra = null;
    public $materia_id = null;
    public $maestro_id = null;

    // Variables de resultados
    public $resultados = [];


    public $searchMaestro = '';
    public $maestros = [];

    //vista tab
    public $vista = 'maestros';
    public $filtro = '';
    //
    public $maestros_con_materias = [];
    public $materias_sin_maestro = [];



    #[Locked]
public $totalAlumnos = 0;

#[Locked]
public $totalMaestros = 0;

#[Locked]
public $materiasSinMaestro = 0;

#[Locked]
public $gruposSinMaestro = 0;
    public function mount()
    {
        $this->calcularEstadisticas();
        $this->cargarDatosIniciales();
    }
    public function obtenerNombreMateriaPorId($id)
    {
        return Materia::where('id', $id)
            ->pluck('nombre')
            ->first();
    }


    public function obtenerNombreMaestroPorId($id)
    {
        return Maestro::where('id', $id)
            ->pluck(DB::raw("CONCAT(apellidos, ' ', name)"))
            ->first();
    }
    public function exportarPDF()
    {
        if (empty($this->resultados)) {
            session()->flash('error', 'No hay resultados para exportar.');
            return;
        }
        Log::info('Vista actual: ' . $this->grado);

        $titulo = match ($this->vista) {
            'maestros' => 'Reporte de Maestros',
            'maestro_por_materias' => 'Reporte de Maestros por materia',
            'materias_sin_maestro' => 'Reporte de materias sin maestro',
            'grupos_sin_maestro' => 'Reporte de grupos sin maestro',
            'alumnos' => 'Reporte de alumnos',
            default => 'Reporte general',
        };





        Log::info('Vista actual: ' . $this->grado);
        $data = [
            'resultados' => $this->resultados,
            'titulo' => $titulo,
            'vista' => $this->vista,
            'grupo_id' => $this->grupo_id,
            'generacion' => $this->generacion,
            'grado' => $this->grado,
            'letra' => $this->letra,
            'materia' => $this->obtenerNombreMateriaPorId($this->materia_id),
            'maestro' => $this->obtenerNombreMaestroPorId($this->maestro_id),
        ];


        Log::info('Vista actual:', $data);


        $pdf = Pdf::loadView('livewire.reportesgrupo_pdf', $data)->setPaper('a4', 'landscape');

        Log::info('Vista actual: ' . $this->grado);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $titulo . '_' . now()->format('Ymd_His') . '.pdf');
    }


    public function cambiarVista($tab)
    {
        $this->vista = $tab;
        $this->resultados = [];
        $this->buscarReporte();
    }

    public function buscarReporte()
    {
        $this->resultados = [];


        switch ($this->vista) {
            case 'alumnos':
                $this->controlEscolar();
                break;
            case 'grupos':
                $this->controlEscolarGrupos();
                break;
            case 'maestros':
                $this->controlEscolarMaestros();
                break;
            case 'maestro_por_materias':
                $this->reporteMaestroPorMateria();
                break;
            case 'materias_sin_maestro':
                $this->reporteMateriasSinMaestro();
                break;
            case 'grupos_sin_maestro':
                $this->reporteGruposSinMaestro();
                break;
            default:
                $this->controlEscolarMaestros();
                break;
        }
    }
    //MIO
    public function reporteMaestroPorMateria()
    {
        $horariosQuery = Horario::with([
            'hora',
            'imparte.maestro',
            'imparte.materia',
            'imparte.grupo'
        ]);


        $horariosQuery->whereHas('imparte', function ($query) {
            if ($this->grupo_id) {
                $query->where('grupo_id', $this->grupo_id);
            }

            if ($this->materia_id) {
                $query->where('materia_id', $this->materia_id);
            }

            if ($this->maestro_id) {
                $query->where('maestro_id', $this->maestro_id);
            }

            if ($this->grado || $this->letra) {
                $query->whereHas('grupo', function ($q) {
                    if ($this->grado) {
                        $q->where('grado', $this->grado);
                    }

                    if ($this->letra) {
                        $q->where('letra', $this->letra);
                    }
                });
            }
        });

        $horarios = $horariosQuery->get();

        $ocupados = [];
        foreach ($horarios as $horario) {
            $clave = $horario->imparte->maestro_id . '_' . $horario->dia . '_' . $horario->hora_id;
            $ocupados[$clave][] = $horario;
        }

        $maestros_con_materias = $horarios->map(function ($horario) use ($ocupados) {
            $clave = $horario->imparte->maestro_id . '_' . $horario->dia . '_' . $horario->hora_id;

            $grupo = $horario->imparte->grupo
                ? $horario->imparte->grupo->grado . $horario->imparte->grupo->letra
                : 'Sin grupo';

            $maestro = $horario->imparte->maestro;
            $nombre_maestro = $maestro->name ?? 'Sin nombre';
            $apellido_maestro = $maestro->apellidos ?? 'Sin apellidos';

            return [
                'maestro' => $nombre_maestro . ' ' . $apellido_maestro,
                'materia' => $horario->imparte->materia->nombre ?? 'Sin materia',
                'grupo' => $grupo,
                'dia' => $horario->dia,
                'hora' => $horario->hora->numero ?? 'Sin número',
                'hora_inicio' => $horario->hora->inicio ?? 'Sin inicio',
                'cruce' => count($ocupados[$clave]) > 1,
            ];
        })->toArray();

        $this->resultados = $maestros_con_materias;
    }
    public function reporteMateriasSinMaestro()
    {
        $query = \App\Models\Imparte::with(['materia', 'grupo'])
            ->whereNull('maestro_id');

        if ($this->materia_id) {
            $query->where('materia_id', $this->materia_id);
        }

        if ($this->grupo_id) {
            $query->where('grupo_id', $this->grupo_id);
        } else {
            if ($this->grado || $this->letra) {
                $query->whereHas('grupo', function ($q) {
                    if ($this->grado) {
                        $q->where('grado', $this->grado);
                    }
                    if ($this->letra) {
                        $q->where('letra', $this->letra);
                    }
                });
            }
        }

        $sinMaestro = $query->get()->map(function ($registro) {
            $grupo = $registro->grupo;
            return [
                'materia' => $registro->materia->nombre ?? 'Sin materia',
                'grupo' => $grupo
                    ? $grupo->grado . $grupo->letra . ' - ' . $grupo->generacion
                    : 'Sin grupo',
            ];
        });

        $this->resultados = $sinMaestro;
    }


    public function reporteGruposSinMaestro()
    {
        $query = DB::table('imparte')
            ->join('grupos', 'imparte.grupo_id', '=', 'grupos.id')
            ->join('materias', 'imparte.materia_id', '=', 'materias.id')
            ->whereNull('imparte.maestro_id');

        if (!empty($this->grado)) {
            $query->where('grupos.grado', $this->grado);
        }
        if (!empty($this->letra)) {
            $query->where('grupos.letra', $this->letra);
        }

        $gruposSinMaestro = $query->select(
            'grupos.id as grupo_id',
            DB::raw("CONCAT(grupos.grado, grupos.letra, '-', grupos.generacion) as grupo_nombre"),
            'materias.nombre as materia_nombre'
        )->get();

        $this->resultados = $gruposSinMaestro;
    }

    public function actualizarMateriaId()
    {
        //dd("Se actualizó a: ", $value);

        $this->materia_id = $this->materia_id !== '' ? (int) $this->materia_id : null;
        //dd("Materia se actualizo a: ", $this->materia_id);
    }


    public function calcularEstadisticas()
    {
        $this->totalAlumnos = Alumno::count();
        $this->totalMaestros = Maestro::count();

        // Materias sin maestro asignado
        $this->materiasSinMaestro = \App\Models\Imparte::whereNull('maestro_id')->count();

        // Grupos sin maestro asignado
        $this->gruposSinMaestro = Grupo::whereHas('imparte', function ($q) {
            $q->whereNull('maestro_id');
        })->distinct()->count('grupos.id');
    }


    public function cargarDatosIniciales()
    {
        $this->resultados = $this->alumnosInscritos()->take(5);
    }
    public function controlEscolar()
    {
        $this->validate([
            'grado' => 'nullable|integer|between:1,3',
            'letra' => 'nullable|string|max:1',
            'generacion' => 'nullable|integer|min:2000|max:' . (date('Y') + 1)
        ]);

        $this->resultados = $this->filtrarResultados();
        $this->calcularEstadisticas();
    }

    public function controlEscolarMaestros()
    {
        $materia_id = $this->materia_id;
        $this->resultados = Maestro::whereHas('materias', function ($query) use ($materia_id) {
            if ($materia_id) {
                $query->where('materias.id', $materia_id);
            }
        })->get();
    }


    /**
     * Método solicitado para DAVIGOD
     * Filtra grupos por generación y un parámetro adicional (ID, letra o grado).
     *
     * USO NORMAL
     * 1. Filtrar solo por generación:
     *    $this->filtrarGrupos('2024');
     *
     * FRILTRAR POR GENERACIÓN + LETRA
     *    $this->filtrarGrupos('2024', 'A', 'letra');
     *
     * FRILTRAR POR GENERACIÓN + GRADO
     *    $this->filtrarGrupos('2024', '1', 'grado');
     *
     * FILTRAR POR GENERACIÓN + ID
     *    $this->filtrarGrupos('2024', 5, 'id');
     *
     */
    public function filtrarResultados()
    {
        return Alumno::whereHas('inscritos.grupo', function ($q) {
            if ($this->grado) {
                $q->where('grado', $this->grado);
            }
            if ($this->letra) {
                $q->where('letra', $this->letra);
            }
            if ($this->generacion) {
                $q->where('generacion', $this->generacion);
            }
        })
            ->with(['inscritos' => function ($q) {
                $q->whereHas('grupo', function ($q2) {
                    if ($this->grado) {
                        $q2->where('grado', $this->grado);
                    }
                    if ($this->letra) {
                        $q2->where('letra', $this->letra);
                    }
                    if ($this->generacion) {
                        $q2->where('generacion', $this->generacion);
                    }
                })->with('grupo');
            }])
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
    }

    public function alumnosInscritos()
    {
        if (!$this->grupo_id) {
            return collect();
        }

        return Grupo::find($this->grupo_id)
            ->alumnos()
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
    }

    public function generacionesDisponibles()
    {
        return Grupo::select('generacion')
            ->distinct()
            ->orderBy('generacion', 'desc')
            ->get()
            ->pluck('generacion');
    }

    public function gruposConAlumnos()
    {
        return Grupo::query()
            ->when($this->generacion, fn($q) => $q->where('generacion', $this->generacion))
            ->withCount('alumnos')
            ->orderBy('grado')
            ->orderBy('letra')
            ->get();
    }

    public function actualizar()
    {
        $this->reset(['grado', 'letra', 'generacion', 'materia_id', 'maestro_id']);
        $this->cargarDatosIniciales();
        $this->calcularEstadisticas();
    }
    public function obtenerMaterias()
    {
        return Materia::select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->toArray();
    }
    public function obtenerMaestrosBasico()
    {
        return Maestro::select('id', 'name', 'apellidos')
            ->orderBy('apellidos')
            ->orderBy('name')
            ->get()
            ->map(function ($maestro) {
                return [
                    'id' => $maestro->id,
                    'nombre_completo' => $maestro->apellidos . ' ' . $maestro->name
                ];
            })
            ->toArray();
    }
    public function obtenerMaestrosCompleto()
    {
        return Maestro::select('id', 'name', 'apellidos', 'telefono', 'curp', 'direccion', 'email')
            ->orderBy('apellidos')
            ->orderBy('name')
            ->get()
            ->map(function ($maestro) {
                return [
                    'id' => $maestro->id,
                    'nombre_completo' => $maestro->apellidos . ' ' . $maestro->name,
                    'telefono' => $maestro->telefono,
                    'curp' => $maestro->curp,
                    'direccion' => $maestro->direccion,
                    'email' => $maestro->email,
                    'tiene_materias' => $maestro->materias()->exists()
                ];
            })
            ->toArray();
    }



    public function render()
    {
        return view('livewire.reportes-grupo', [
            'grupos' => $this->gruposConAlumnos(),
            'generaciones' => $this->generacionesDisponibles(),
            'materias' => $this->obtenerMaterias(),

            'maestros_basico' => $this->obtenerMaestrosBasico(),
            'maestros_completo' => $this->obtenerMaestrosCompleto(),
            'resultados' => $this->resultados,
            'totalAlumnos' => $this->totalAlumnos,
            'totalMaestros' => $this->totalMaestros,
            'materiasSinMaestro' => $this->materiasSinMaestro,
            'gruposSinMaestro' => $this->gruposSinMaestro,
            'materia_id' => $this->materia_id
        ]);
    }
}
