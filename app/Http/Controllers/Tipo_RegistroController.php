<?php

namespace App\Http\Controllers;

use App\Models\Tipo_Registro;
use Illuminate\Http\Request;

class Tipo_RegistroController extends Controller
{
    public function getTipos(){
        $registro = Tipo_Registro::all();
        return response()->json($registro);
    }
}
