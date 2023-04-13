<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    public function registrarUbicaciones($asistencia_id, $listaUbicacion){
        $response = [];

        
        if ($listaUbicacion) {
            //var_dump($listaUbicacion); die();

            foreach($listaUbicacion as $lu){
                $newUbicacion = new Ubicacion();
                $newUbicacion->asistencia_id = $asistencia_id;
                $newUbicacion->latitud = $lu['latitud'];
                $newUbicacion->longitud = $lu['longitud'];
                $newUbicacion->save();
            }

            $response = [ 'status' => true, 'message' => 'se registro las ubicaciones', 'data' => $newUbicacion];
        }else {
            $response = [ 'status' => false, 'message' => 'no hay ubicaciones', 'data' => null ];
        }
        return $response;
    }
}
