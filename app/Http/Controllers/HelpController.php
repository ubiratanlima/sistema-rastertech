<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Exibe o Manual de Operações Rastertech (Hub de Inteligência).
     */
    public function index()
    {
        return view('help.index');
    }
}
