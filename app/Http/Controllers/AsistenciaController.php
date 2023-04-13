<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    private $ubicacionCtrl;

    public function __construct()
    {
        $this->ubicacionCtrl = new UbicacionController();

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
                                    ->where('tipo_registro_id', $requestAsistencia->tipo_registro_id)
                                    ->get()->first();

           /*  if ($existeTipo) {
                //
                $response = [
                    'status' => false,
                    'mensaje' => 'Existe una asistencia de tipo ' .$existeTipo->tipo_registro->tipo,
                ];
            }
            else{ */
                if ($newAsistencia->save()) {
                    //registro la ubicaciones

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
           /*  } */
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
