<div class="container-fluid p-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-calendar-times text-danger me-2"></i>
                    Registro de Inasistencias
                </h2>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-success" wire:click="agregar">
                        <i class="fas fa-plus me-1"></i>Nueva Inasistencia
                    </button>
                    @if($inasistencias && $inasistencias->count() > 0)
                        <button type="button" class="btn btn-info" wire:click="generarPDF">
                            <i class="fas fa-file-pdf me-1"></i>Generar PDF
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes de éxito/error -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulario de inasistencia -->
    @if($mostrarFormulario)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            {{ isset($inasistencia['id']) ? 'Modificar Inasistencia' : 'Nueva Inasistencia' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="guardar">
                            <div class="row">
                                <!-- Fecha -->
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <label for="fechaSeleccionada" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>Fecha <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="fechaSeleccionada"
                                           wire:model.live="fechaSeleccionada"
                                           required>
                                    <small class="form-text text-muted">
                                        Día calculado: <strong>{{ $diaCalculado }}</strong>
                                    </small>
                                    @error('inasistencia.fecha')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Maestro -->
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <label for="maestroSeleccionado" class="form-label">
                                        <i class="fas fa-user-tie me-1"></i>Maestro <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" 
                                            id="maestroSeleccionado"
                                            wire:model.live="maestroSeleccionado"
                                            required>
                                        <option value="">Seleccionar maestro...</option>
                                        @if($maestros)
                                            @foreach($maestros as $maestro)
                                                <option value="{{ $maestro->id }}">
                                                    {{ $maestro->name }} {{ $maestro->apellidos }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('maestroSeleccionado')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Grupo -->
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <label for="grupoSeleccionado" class="form-label">
                                        <i class="fas fa-users me-1"></i>Grupo <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" 
                                            id="grupoSeleccionado"
                                            wire:model.live="grupoSeleccionado"
                                            {{ !$maestroSeleccionado ? 'disabled' : '' }}
                                            required>
                                        <option value="">Seleccionar grupo...</option>
                                        @if($grupos && $maestroSeleccionado)
                                            @foreach($grupos as $grupo)
                                                <option value="{{ $grupo->id }}">
                                                    {{ $grupo->grado }}° {{ $grupo->letra }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('grupoSeleccionado')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Materia -->
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <label for="materiaSeleccionada" class="form-label">
                                        <i class="fas fa-book me-1"></i>Materia <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" 
                                            id="materiaSeleccionada"
                                            wire:model.live="materiaSeleccionada"
                                            {{ !$grupoSeleccionado ? 'disabled' : '' }}
                                            required>
                                        <option value="">Seleccionar materia...</option>
                                        @if($materias && $grupoSeleccionado)
                                            @foreach($materias as $materia)
                                                <option value="{{ $materia->id }}">
                                                    {{ $materia->nombre }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('materiaSeleccionada')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Horario -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="horarioSeleccionado" class="form-label">
                                        <i class="fas fa-clock me-1"></i>Horario <span class="text-danger">*</span>
                                    </label>
                                    
                                    @if($cargandoHorarios)
                                        <div class="text-center p-3">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Cargando horarios...</span>
                                            </div>
                                            <div class="mt-2 text-muted">Cargando horarios disponibles...</div>
                                        </div>
                                    @elseif(empty($horariosDisponibles))
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            @if(!$materiaSeleccionada)
                                                Seleccione maestro, grupo y materia para ver los horarios disponibles.
                                            @else
                                                No hay horarios programados para esta combinación en {{ $diaCalculado }}.
                                            @endif
                                        </div>
                                    @else
                                        <div class="row">
                                            @foreach($horariosDisponibles as $horario)
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                               type="radio" 
                                                               name="horarioSeleccionado" 
                                                               id="horario_{{ $horario['id'] }}"
                                                               value="{{ $horario['id'] }}" 
                                                               wire:model="horarioSeleccionado"
                                                               {{ $horario['ya_registrada'] ? 'disabled' : '' }}>
                                                        <label class="form-check-label {{ $horario['ya_registrada'] ? 'text-muted' : '' }}" 
                                                               for="horario_{{ $horario['id'] }}">
                                                            {{ $horario['hora_texto'] }}
                                                            @if($horario['ya_registrada'])
                                                                <br><small class="text-danger">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Ya registrada
                                                                </small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @error('horarioSeleccionado')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Justificación -->
                                <div class="col-md-6 mb-3">
                                    <label for="justificacion" class="form-label">
                                        <i class="fas fa-comment me-1"></i>Justificación
                                    </label>
                                    <textarea class="form-control" 
                                              id="justificacion"
                                              wire:model="inasistencia.justificacion"
                                              rows="4"
                                              maxlength="1000"
                                              placeholder="Motivo de la inasistencia (opcional)"></textarea>
                                    <small class="form-text text-muted">
                                        Máximo 1000 caracteres
                                    </small>
                                    @error('inasistencia.justificacion')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            {{ isset($inasistencia['id']) ? 'Actualizar' : 'Guardar' }}
                                        </button>
                                        <button type="button" class="btn btn-secondary" wire:click="cancelar">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla de inasistencias -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Inasistencias Registradas
                            @if($inasistencias && $inasistencias->count() > 0)
                                <span class="badge bg-primary">{{ $inasistencias->count() }}</span>
                            @endif
                        </h5>
                        @if(!$mostrarFormulario && $seleccionado >= 0)
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-warning" wire:click="modificar">
                                    <i class="fas fa-edit me-1"></i>Modificar
                                </button>
                                <button type="button" 
                                        class="btn btn-danger" 
                                        wire:click="eliminar"
                                        onclick="return confirm('¿Está seguro de eliminar esta inasistencia?')">
                                    <i class="fas fa-trash me-1"></i>Eliminar
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($inasistencias && $inasistencias->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Fecha</th>
                                        <th>Maestro</th>
                                        <th>Grupo</th>
                                        <th>Materia</th>
                                        <th>Horario</th>
                                        <th>Justificación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inasistencias as $index => $inasistencia)
                                        <tr class="{{ $seleccionado == $index ? 'table-active' : '' }} cursor-pointer"
                                            wire:click="seleccionarInasistencia({{ $index }})"
                                            style="cursor: pointer;">
                                            <td>
                                                @if($seleccionado == $index)
                                                    <i class="fas fa-check-circle text-primary"></i>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold">
                                                    {{ \Carbon\Carbon::parse($inasistencia->fecha)->format('d/m/Y') }}
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($inasistencia->fecha)->locale('es')->isoFormat('dddd') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($inasistencia->imparte && $inasistencia->imparte->maestro)
                                                    {{ $inasistencia->imparte->maestro->name }}
                                                    {{ $inasistencia->imparte->maestro->apellidos }}
                                                @else
                                                    <span class="text-muted">Sin maestro</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($inasistencia->imparte && $inasistencia->imparte->grupo)
                                                    <span class="badge bg-info">
                                                        {{ $inasistencia->imparte->grupo->grado }}° 
                                                        {{ $inasistencia->imparte->grupo->letra }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Sin grupo</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($inasistencia->imparte && $inasistencia->imparte->materia)
                                                    {{ $inasistencia->imparte->materia->nombre }}
                                                @else
                                                    <span class="text-muted">Sin materia</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($inasistencia->horario)
                                                    <strong>{{ $inasistencia->horario->dia_semana }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $horasClase[$inasistencia->horario->hora_numero] ?? 'Hora desconocida' }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">Sin horario</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($inasistencia->justificacion)
                                                    <span class="text-truncate d-inline-block" 
                                                          style="max-width: 200px;" 
                                                          title="{{ $inasistencia->justificacion }}">
                                                        {{ $inasistencia->justificacion }}
                                                    </span>
                                                @else
                                                    <span class="text-muted fst-italic">Sin justificación</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-calendar-times fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No hay inasistencias registradas</h5>
                            <p class="text-muted">Haga clic en "Nueva Inasistencia" para comenzar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    @if($inasistencias && $inasistencias->count() > 0)
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Información:</strong> 
                    Haga clic en cualquier fila de la tabla para seleccionar una inasistencia y poder modificarla o eliminarla.
                    Total de inasistencias registradas: <strong>{{ $inasistencias->count() }}</strong>
                </div>
            </div>
        </div>
    @endif
</div>