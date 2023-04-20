<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{

    public function cargarUbicaciones(){

        // Definir el array de posiciones
        
        $posiciones = [
            ['latitud' => 40.4167754, 'longitud' => -3.7037902],
            ['latitud' => 37.7749295, 'longitud' => -122.4194155],
            ['latitud' => 51.5073509, 'longitud' => -0.1277583],
        ];
    
        /*
        $posiciones = [
            ['latitud' => -2.228510, 'longitud' => -80.858213],
            ['latitud' => -2.228514, 'longitud' => -80.858136],
            ['latitud' => -2.228527, 'longitud' => -80.858074],
            ['latitud' => -2.228562, 'longitud' => -80.858217],
            ['latitud' => -2.228576, 'longitud' => -80.858139],
            ['latitud' => -2.228581, 'longitud' => -80.858080],     
              
        ];
        */
        

        // Definir la latitud y longitud a validar
        
        $latitud = 40.4167754;
        $longitud = -3.7037902;

        
        /*
        $longitud = -2.228546;
        $latitud = -80.858167;
        */
        
        // Convertir el array de posiciones a un array de strings
        $posicionesStrings = array_map(function($posicion) {
            return $posicion['latitud'] . ',' . $posicion['longitud'];
        }, $posiciones);

        // Comprobar si la latitud y longitud están dentro del array de posiciones
        if (in_array($latitud . ',' . $longitud, $posicionesStrings)) {
            echo 'La posición está dentro del array de posiciones. :D.';
        } else {
            echo 'La posición no está dentro del array de posiciones.';
        }



    }


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
