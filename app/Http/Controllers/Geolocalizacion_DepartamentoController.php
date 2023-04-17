<?php

namespace App\Http\Controllers;

use App\Models\Geolocalizacion_Departamento;
use Illuminate\Http\Request;

class Geolocalizacion_DepartamentoController extends Controller
{
    //

    public function validarGeolocalizacion(){

        //$requestUbicaciones = (object) $request->ubicacion;

        $listaUbicacion  = collect($listaUbicacion = [
              "latitud" => -2.233293,
              "longitud" => -81.929581
        ]);

        if ($listaUbicacion) {
            $latitud = $listaUbicacion['latitud'];
            $longitud = $listaUbicacion['longitud'];

        
            $dataGeoLocalizacion = Geolocalizacion_Departamento::all(); 

            $groupedLat = $dataGeoLocalizacion->mapToGroups(function ($item) { 
                return ['lat' => $item['lat']]; 
            });

            $groupedLog = $dataGeoLocalizacion->mapToGroups(function ($item) { 
                return ['log' => $item['log']]; 
            });

            $dataLat = collect($groupedLat)->get('lat');
            $dataLog = collect($groupedLog)->get('log');

            //"latitud" => -2.233294,  viene por request o la app
            //"longitud" => -80.879810 viene por request o la app

            $dataMinMaxLat = [
                'min' => $dataLat->max(),  // "min": "-2.232592",
                'max' => $dataLat->min()  //"max": "-2.233294"
            ];
            
            $dataMinMaxLog = [ 
                'min' => $dataLog->max(), 
                'max' => $dataLog->min()
            ];
            
            //return response()->json($dataMinMaxLat); die();
           

            /* $newGeoLocalizacionLat = Geolocalizacion_Departamento::where('lat', '>=', $dataMinMaxLat['min'])//minlat  100
                                                            ->where('lat',$latitud)//101  inicioRequestlatitud
                                                            ->where('lat', '<=', $dataMinMaxLat['max'])//maxLat 1000
                                                            ->get();

            

            $newGeoLocalizacionLog = Geolocalizacion_Departamento::where('log', '<=', $dataMinMaxLog['min'])
                                                            ->where('log', '<=', $dataMinMaxLog['max'])
                                                            ->where('log', $longitud)//fin
                                                            ->get(); */

                                                            
            $newGeoLocalizacionLat = Geolocalizacion_Departamento::where('lat', '>=', $dataMinMaxLat['min'])//minlat  100
                                                                ->where('lat',$latitud)//101  inicioRequestlatitud
                                                                ->where('lat', '<=', $dataMinMaxLat['max'])//maxLat 1000
                                                                ->get();



            $newGeoLocalizacionLog = Geolocalizacion_Departamento::where('log', '<=', $dataMinMaxLog['min'])
                                                                ->where('log', '<=', $dataMinMaxLog['max'])
                                                                ->where('log', $longitud)//fin
                                                                ->get();
                
            
            
            return response()->json($newGeoLocalizacionLat);

        }

    }
}
