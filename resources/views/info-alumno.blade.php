@extends('layouts.app')

@section('content')
    @livewire('info-alumno', ['alumnoId' => $alumnoId ?? request()->route('alumnoId')])
@endsection
