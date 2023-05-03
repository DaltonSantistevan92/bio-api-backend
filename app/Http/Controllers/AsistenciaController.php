<?php

namespace App\Http\Controllers;

use App\Models\{Asistencia,Tipo_Asistencia,Asistencia_Eventos, Asistencias_Departamentos, Evento};
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    private $ubicacionCtrl;
    private $eventnCtrl;
    private $geoDepCtrl;


    public function __construct()
    {
        $this->ubicacionCtrl = new UbicacionController();
        $this->eventnCtrl = new EventoController();
        $this->geoDepCtrl = new Geolocalizacion_DepartamentoController();
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

    public function buscarUltimoTipo($user_id){
        $asistenciaUltimo = Asistencia::where('user_id',$user_id)->where('fecha', date('Y-m-d'))->get();

        if ($asistenciaUltimo->count() > 0) {
            foreach($asistenciaUltimo as $item){
                $ultimoTipo = $item->tipo_registro_id;
            }
        } else {
            $ultimoTipo = '';
        }                               
        return response()->json($ultimoTipo);  
    }

    public function buscarUltimoTipoAsistencia($user_id){
        //preguntar si existe un evento
        $evento = Evento::where('fecha', date('Y-m-d'))->get()->first();
        if ($evento) {
            $response = [
                'status' => true,
                'message' => 'Existe el evento '. $evento->nombre,
                'data' => $evento,
                'tipo_asistencia_id' => 2
            ];
        }else{
            //verificar si existe el usuario en la tabla asistencia
            $existeUserAsistencia = Asistencia::where('user_id',$user_id)->where('fecha', date('Y-m-d'))->get()->first();

            if ($existeUserAsistencia) {

                $asistenciaUltimo = Asistencia::where('user_id',$user_id)->where('fecha', date('Y-m-d'))->get();

                if ($asistenciaUltimo->count() > 0 ) {
                    foreach($asistenciaUltimo as $item){
                        $ultimoTipoAsistencia = $item->tipo_asistencia_id;
                    }

                    $response = [
                        'status' => true,
                        'message' => 'existe datos',
                        'tipo_asistencia_id' => $ultimoTipoAsistencia
                    ];
                    
                }else {
                    $response = [
                        'status' => false,
                        'message' => 'No hay datos para procesar',
                        'tipo_asistencia_id' => 0
                    ];
                }  
            }else {
                $response = [
                    'status' => false,
                    'message' => 'Se va hacer el primer registro para el usuario',
                    'tipo_asistencia_id' => 1
                ];
            }
        }
        return response()->json($response);  
    }

    public function registrarAsistencia(Request $request){
        $requestAsistencia = (object) $request->asistencia;
        $requestUbicaciones = (object) $request->ubicacion;
        $response = [];

        if ($requestAsistencia) {

            if ($requestAsistencia->tipo_asistencia_id === 1) {//asistencia

                $requUbicacion = $this->geoDepCtrl->validarGeolocalizacion( $requestUbicaciones );

                if ($requUbicacion['status'] == true) {  //Validar con las ubicaciones  de la table geolocalizacion 
                    $departamento_id = $requUbicacion['ubicacion']->departamento_id;

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

                            $registraAsitenciaDepartamento = new Asistencias_Departamentos();
                            $registraAsitenciaDepartamento->asistencia_id = $newAsistencia->id;
                            $registraAsitenciaDepartamento->departamento_id = $departamento_id;
                            $registraAsitenciaDepartamento->save();

                            $response = [
                                'status' => true,
                                'message' => 'La asistencia se registro correctamente',
                                'data' => [
                                    'asistencia' => $newAsistencia,
                                    'ubicacion' => $respUbicacion,
                                    'asistencia_departamento' => $registraAsitenciaDepartamento
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
                }else {
                    $response = [
                        'status' => false,
                        'message' => $requUbicacion['message'],
                    ];
                }
            }else{//evento
                $returnEvento = $this->eventnCtrl->buscarEventos(date('Y-m-d'));

                if ($returnEvento['status'] === true) {//si hay eventos
                    $newAsistencia = $this->saveAsistencia($requestAsistencia);
                    $evento_id = $returnEvento['evento_id'];

                    $existeTipo = Asistencia::where('user_id', $requestAsistencia->user_id)
                                        ->where('tipo_asistencia_id' ,'=', 2)
                                        ->where('fecha',date('Y-m-d'))
                                        ->get()->count();

                    if ($existeTipo === 4) {
                        $response = [
                            'status' => false,
                            'message' => 'Cumplio sus horas laborables el dia : ' .date('Y-m-d'),
                        ];
                    } else{
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
                                'message' => 'No se pudo registrar la asistencia',
                            ];
                        }
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


    public function reporteTrabajador($user_id, $f_inicio, $f_fin, $tipo_asistencia_id){//solo trabajador
        $response = [];

        $asistencias = Asistencia::where('estado','A')->where('user_id',intval($user_id))
                                    ->where('fecha', '>=', $f_inicio)->where('fecha', '<=', $f_fin)
                                    ->where('tipo_asistencia_id', intval($tipo_asistencia_id))->get();

        if (count($asistencias) > 0) {

            if (intval($tipo_asistencia_id) == 1) {
                foreach($asistencias as $item){
                    $item->user->persona;
                    $item->tipo_asistencia;
                    $item->tipo_registro;

                    foreach($item->ubicacion as $ubi){
                        $ubi;
                    }
    
                    foreach( $item->asistencias_departamento as $ad){
                        $ad->departamento;
                    }
                }
                $response = [
                    'status' => true,
                    'message' => 'existen datos de asistencias',
                    'data' => $asistencias,
                    'datos_personales' => [
                        'user' => $item->user,
                    ]
                ];  
            }else{
                foreach($asistencias as $item){
                    $item->user->persona;
                    $item->tipo_asistencia;
                    $item->tipo_registro;
    
                    foreach($item->asistencia_evento as $asev){
                        $asev->evento;
                    }
                }
    
                $response = [
                    'status' => true,
                    'message' => 'existen datos de evento',
                    'data' => $asistencias
                ]; 
            }
        }else{
            $response = [
                'status' => false,
                'message' => 'no existen datos',
                'data' => null
            ];
        }  
        return response()->json($response);
    }


    public function reporteSuperAdminAndAdministrador($f_inicio, $f_fin, $tipo_asistencia_id){
        $returnResponse = [];

        $asistencias = Asistencia::where('estado','A')
                            ->where('fecha', '>=', $f_inicio)->where('fecha', '<=', $f_fin)
                            ->where('tipo_asistencia_id', intval($tipo_asistencia_id))->get();

        $returnResponse = $this->consultarTipoAsistencias($asistencias, intval($tipo_asistencia_id));

        return response()->json($returnResponse); 
    }


    private function consultarTipoAsistencias($asistencias, $tipo_asistencia_id){
        if ( count($asistencias) > 0 ) {
    
            if ( intval($tipo_asistencia_id) == 1 ) {
                foreach($asistencias as $item){
                    $item->user->persona;
                    $item->tipo_asistencia;
                    $item->tipo_registro;

                    foreach($item->ubicacion as $ubi){
                        $ubi;
                    }
    
                    foreach( $item->asistencias_departamento as $ad){
                        $ad->departamento;
                    }
                }
                $response = [
                    'status' => true,
                    'message' => 'existen datos de asistencias',
                    'data' => $asistencias,
                ];  
            }else{
                foreach($asistencias as $item){
                    $item->user->persona;
                    $item->tipo_asistencia;
                    $item->tipo_registro;
    
                    foreach($item->asistencia_evento as $asev){
                        $asev->evento;
                    }
                }
    
                $response = [
                    'status' => true,
                    'message' => 'existen datos de evento',
                    'data' => $asistencias
                ]; 
            }
        }else{
            $response = [
                'status' => false,
                'message' => 'no existen datos',
                'data' => null
            ];
        } 
        return $response;
    } 
}
