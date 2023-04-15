<?php

namespace App\Http\Controllers;

use App\Models\{Asistencia,Tipo_Asistencia,Asistencia_Eventos};
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    private $ubicacionCtrl;
    private $eventnCtrl;

    public function __construct()
    {
        $this->ubicacionCtrl = new UbicacionController();
        $this->eventnCtrl = new EventoController();
    }

    public function cargarTipoAsistencia(){
        $response = [];
        $tipo_asistencia = Tipo_Asistencia::all();

        if($tipo_asistencia->count() > 0){
          $response = ['status' => true, 'message' => 'Tipos de asistencia cargadas con éxito.', 'data' => $tipo_asistencia ];
        }else{
          $response = ['status' => false, 'message' => 'No hay tipos de Asistencia.', 'data' => null ];
        }

        return response()->json($response, 200);
    } 

    public function buscarUltimaAsistencia($user_id){
        $asistenciaUltimo = Asistencia::where('user_id',$user_id)->where('tipo_asistencia_id' ,'=', 1)->where('fecha', date('Y-m-d'))->get();

        if ($asistenciaUltimo->count() > 0) {
            foreach($asistenciaUltimo as $item){
                $ultimoTipo = $item->tipo_registro_id;
            }
        } else {
            $ultimoTipo = '';
        }                               
        return response()->json($ultimoTipo);  
    }


    /* public function registrarAsistencia(Request $request){
        $requestAsistencia = (object) $request->asistencia;
        $requestUbicaciones = (object) $request->ubicacion;


        //Validar con las ubicaciones  de la table geolocalizacion 
        
        
        $response = [];

        if ($requestAsistencia) {
            $newAsistencia = new Asistencia();
            $newAsistencia->user_id = $requestAsistencia->user_id;
            $newAsistencia->tipo_asistencia_id = $requestAsistencia->tipo_asistencia_id;  //Tipo de Asistencia
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
    } */


    public function registrarAsistencia(Request $request){
        $requestAsistencia = (object) $request->asistencia;
        $requestUbicaciones = (object) $request->ubicacion;
        $response = [];


        //Validar con las ubicaciones  de la table geolocalizacion 

        if ($requestAsistencia) {

            if ($requestAsistencia->tipo_asistencia_id === 1) {//asistencia
                $newAsistencia = $this->saveAsistencia($requestAsistencia);

                $existeTipo = Asistencia::where('user_id', $requestAsistencia->user_id)
                                    ->where('tipo_asistencia_id' ,'=', 1)
                                    ->where('fecha',date('Y-m-d'))
                                    ->get()->count();

                if ($existeTipo === 4) {
                    $response = [
                        'status' => false,
                        'message' => 'Cumplio sus horas laborables el dia : ' .date('Y-m-d'),
                    ];
                } else {
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
            }else{//evento
                $returnEvento = $this->eventnCtrl->buscarEventos(date('Y-m-d'));

                if ($returnEvento['status'] === true) {//si hay eventos
                    $newAsistencia = $this->saveAsistencia($requestAsistencia);
                    $evento_id = $returnEvento['evento_id'];

                    if ($newAsistencia->save()) {
                        $newAsistenciaEventos = new Asistencia_Eventos();
                        $newAsistenciaEventos->asistencia_id = $newAsistencia->id;
                        $newAsistenciaEventos->evento_id = $evento_id;
                        $newAsistenciaEventos->save();

                        $response = [
                            'status' => true,
                            'message' => 'La asistencia se registro correctamente',
                        ];
                    }else{
                        $response = [
                            'status' => false,
                            'message' => 'ERROR',
                        ];
                    }
                }else {//no hay eventos
                    $response = [
                        'status' => false,
                        'message' => $returnEvento['message'],
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


    private function saveAsistencia($requestAsistencia){
        $newAsistencia = new Asistencia();
        $newAsistencia->user_id = $requestAsistencia->user_id;
        $newAsistencia->tipo_asistencia_id = $requestAsistencia->tipo_asistencia_id;  //Tipo de Asistencia
        $newAsistencia->tipo_registro_id = $requestAsistencia->tipo_registro_id;//1 entrada
        $newAsistencia->fecha = date('Y-m-d');
        $newAsistencia->hora = date('H:i:s');
        $newAsistencia->estado = 'A';
        return $newAsistencia;
    }
}
