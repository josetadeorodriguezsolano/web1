<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index()
    {
        return view('horarios.index');
    }
}