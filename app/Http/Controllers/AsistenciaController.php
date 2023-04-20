<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Tipo_asistencia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    private $ubicacionCtrl;

    public function __construct()
    {
        $this->ubicacionCtrl = new UbicacionController();
        //Hola mundo desde el backend
    }

    public function cargarTipoAsistencia(){
        $response = [];
        $tipo_asistencia = Tipo_asistencia::all();

        if($tipo_asistencia->count() > 0){
            $response = ['status' => true, 'message' => 'Tipos de Asistencia cargados correctamente', 'data' => $tipo_asistencia];

        }else{
            $response = ['status' => false, 'message' => 'No existen tipos de asistencias', 'data' => null];
        }

        return response()->json($response, 200);

    }

    public function buscarUltimaAsistencia($user_id){
        $asistenciaUltimo = Asistencia::where('user_id',$user_id)
                                        ->where('fecha', date('Y-m-d'))->get();

        if ($asistenciaUltimo->count() > 0) {
            foreach($asistenciaUltimo as $item){
                $ultimoTipo = $item->tipo_registro_id;
            }
        } else {
            $ultimoTipo = '';
        }                               
        return response()->json($ultimoTipo);  
    }

    

    public function registrarAsistencia(Request $request){
        $requestAsistencia = (object) $request->asistencia;
        $requestUbicaciones = (object) $request->ubicacion;


        //Validar con las ubicones  de la table geolocalizacion de agencia
        
        $response = [];

        if ($requestAsistencia) {
            $newAsistencia = new Asistencia();
            $newAsistencia->user_id = $requestAsistencia->user_id;
            $newAsistencia->tipo_registro_id = $requestAsistencia->tipo_registro_id;//1 entrada
            $newAsistencia->fecha = date('Y-m-d');
            $newAsistencia->hora = date('H:i:s');
            $newAsistencia->estado = 'A';

            $existeTipo = Asistencia::where('user_id', $requestAsistencia->user_id)
                                    ->where('fecha',date('Y-m-d'))
                                    ->get()->count();

            if ($existeTipo === 4) {
                $response = [
                    'status' => false,
                    'message' => 'Cumplio sus horas laborables el dia : ' .date('Y-m-d'),
                ];
            }
            else{
                if ($newAsistencia->save()) {
                    $respUbicacion = $this->ubicacionCtrl->registrarUbicaciones($newAsistencia->id, $requestUbicaciones);

                    $response = [
                        'status' => true,
                        'message' => 'La asistencia se registro correctamente',
                        'data' => [
                            'asistencia' => $newAsistencia,
                            'ubicacion' => $respUbicacion
                        ]
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'No se puede registrar la asistencia',
                        'data' => null,
                    ];
                }
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'No hay datos para procesar',
                'data' => null,
            ];
        }
        return response()->json($response);
    }

    public function getDateTime(){
        $response = [];
        $response = ['fecha' => date('Y-m-d'),'hora' => date('H:i:s')];

        return response()->json($response);
    }
}
