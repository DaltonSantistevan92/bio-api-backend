<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Geolocalizacion_Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    //
  
    public function getAllDepartamentos(){
        $response = [];
        $departamentos = Departamento::all();

        if($departamentos->count() > 0){

            foreach($departamentos as $item){
                $item->geolocalizacion_departamento;
            }

            $response = [
                'status' => true, 
                'message' => 'Departamentos cargadas con éxito.', 
                'data' => $departamentos 
            ];
        }else{
            $response = ['status' => false, 'message' => 'No hay departamentos.', 'data' => null ];
        }
        return response()->json($response);
    }

    public function createDepartamento(Request $request){

        $requestDepartamento = (object) $request->departamento;
        $response = [];

        if($requestDepartamento){

            //CREAR DEPARTAMENTO
            $newDepartamento = new Departamento();
            $newDepartamento->nombre = $requestDepartamento->nombre;
            $newDepartamento->estado = "A";

            if($newDepartamento->save()){

                //CREAR LA GEOLOCALIZACION DEL DEPART
                $response = [
                    'status' => true,
                    'message' => 'El departamento ' .$newDepartamento->nombre. 'se ha registrado con éxito.'                    
                ];
            }else{
                $response = [
                    'status' => false, 
                    'message' => 'Error. El departamento no se pudo registrar.' 
                ];
            }
        }else{
            $response = ['status' => false, 'message' => 'No existen datos para procesar.' ];
        }

        return response()->json($response, 200);
    }

    public function updateDepartamento(Request $request){
        $requestDepartamento = (object) $request->departamento;
        $requestGeolocalizacion_Departamento = (object) $request->geolocalizacion_departamento;
        $response = [];

        $dataDepartamento = Departamento::find($requestDepartamento->id);

        if($dataDepartamento){

            //Editar Dertamento
            $dataDepartamento->nombre = $requestDepartamento->nombre;
            $dataDepartamento->estado = 'A';

            //Editar Geolocalizacion Departamento
            foreach($requestGeolocalizacion_Departamento as $item){
                $newGeolocalizacion_Departamento = new Geolocalizacion_Departamento;
                $newGeolocalizacion_Departamento->id = $requestDepartamento->departamento_id;
                $newGeolocalizacion_Departamento->lat = $item->lat;
                $newGeolocalizacion_Departamento->log = $item->log;
                $newGeolocalizacion_Departamento->save();
            }


            if($dataDepartamento->save()){
                $response = [
                    'status' => true,
                    'message' => 'El departamento ' .$dataDepartamento->nombre. 'se ha actualizado con éxito.'                    
                ];

            }else{
                $response = [
                    'status' => false,
                    'message' => 'Error. No se puede actualizar este departamento.'                    
                ];
            }
        }else{
            $response = [
                'status' => false, 
                'message' => 'No existen datos para procesar.' ];
        }
        return response()->json($response);

    }

    public function deleteDepartamento($id){
        $dataDepartamento = Departamento::find(intval($id));
        $response = [];

        if($dataDepartamento){
            $dataDepartamento->estado = "I";

            if($dataDepartamento->save()){
                $response = [
                    'status' => true,
                    'message' => 'El departamento ha sido eliminado correctamente.',
                ];
            }else{
                $response = [
                    'status' => false,
                    'message' => 'Error, No se ha podido dar de baja este departemento.',
                ];

            }
        }else{
            $response = [
                'status' => false,
                'message' => 'Error. No hay datos de este departamento',
            ];

        }
        return response()->json($response);
    }


}
