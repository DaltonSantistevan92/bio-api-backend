<?php

namespace App\Http\Controllers;

use App\Models\Sexo;
use Illuminate\Http\Request;

class SexoController extends Controller
{
    public function listarSexo(){
        $sexo = Sexo::where('status','A')->get();
        $response = [];

        if (count($sexo) > 0) {
            $response = [
                'status' => true,
                'message' => 'existe datos',
                'data' => $sexo,
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'no existe datos',
                'data' => null,
            ];
        }
        return response()->json($response);
    }
}
