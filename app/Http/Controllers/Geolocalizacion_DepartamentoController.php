<?php

namespace App\Http\Controllers;

use App\Models\Geolocalizacion_Departamento;
use Illuminate\Http\Request;

class Geolocalizacion_DepartamentoController extends Controller
{

    private function buscarUbicacion($latitud,$longitud)
    {
        $ubicacion = Geolocalizacion_Departamento::select()
            ->whereBetween('lat', [$latitud - 0.001, $latitud + 0.001])
            ->whereBetween('log', [$longitud - 0.001, $longitud + 0.001])
            ->orderByRaw("ST_Distance_Sphere(point(log, lat), point($longitud, $latitud))")
            ->first();

        if ($ubicacion) {
            $response = [
                'status' => true,
                'message' => 'La ubicacion existe en la bd',
                'ubicacion' => $ubicacion
            ];
            return $response;
        } else {
            $response = [
                'status' => false,
                'message' => 'Ubicacion no establecida..!!',
            ];
            return $response;
        }
    }

    public function validarGeolocalizacion($listaUbicacion){

        if ($listaUbicacion) {
            foreach($listaUbicacion as $lu){
                $latitud = $lu['latitud'];
                $longitud = $lu['longitud'];
            }
            $resp = $this->buscarUbicacion($latitud, $longitud);
            return $resp; 
        }
    }
}
