<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class EventoController extends Controller
{

    public function buscarEventos($fecha)
    {
        $response = [];
        $event = Evento::whereDate('fecha', $fecha)->get()->first();

        if ($event) {
            $response = [
                'status' => true,
                'evento_id' => $event->id,
                'message' => 'existe evento',
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'No hay eventos disponibles',
            ];
        }
        return $response;
    }

    public function listarEvento()
    {
        $response = [];
        $event = Evento::all();

        if ($event->count() > 0) {
            $response = [
                'status' => true,
                'message' => 'existe evento',
                'data' => $event,
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'No hay eventos',
                'data' => null,
            ];
        }
        return response()->json($response);
    }


    public function eliminarEvento($id)
    {
        $event = Evento::find($id);
        $response = [];

        if ($event) {
            $event->estado = 'I';

            if ($event->save()) {
                $response = [
                    'status' => true,
                    'message' => 'Se elimino el evento ' . $event->nombre,
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'No se puede elimino el evento',
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'No hay datos para procesar',
            ];
        }
        return response()->json($response);
    }

    public function guardarEvento(Request $request)
    {
        $dataEvento = (object) $request->evento;
        $response = [];

        if ($dataEvento) {
            $newEvent = new Evento();
            $newEvent->nombre = $dataEvento->nombre;
            $newEvent->fecha = $dataEvento->fecha;
            $newEvent->estado = 'A';

            if ($newEvent->save()) {
                $response = [
                    'status' => true,
                    'message' => 'Se registro el evento ' . $newEvent->nombre,
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'No se puede registro el evento',
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'No hay datos para procesar',
            ];
        }
        return response()->json($response);
    }

    public function editarEvento(Request $request)
    {
        $dataEvento = (object) $request->evento;
        $event = Evento::find(intval($dataEvento->id));
        $response = [];

        if ($event) {
            $event->nombre = $dataEvento->nombre;
            $event->fecha = $dataEvento->fecha;
            $event->estado = 'A';

            $existeNombre = Evento::where('nombre', $dataEvento->nombre)->get()->first();
            $existeFecha = Evento::where('fecha', $dataEvento->fecha)->get()->first();

            if ($existeNombre) {
                $response = [
                    'status' => false,
                    'message' => 'Existe el nombre del evento: ' . $existeNombre->nombre . ' vuelva a modificar',
                ];
            } else if ($existeFecha) {
                $response = [
                    'status' => false,
                    'message' => 'Existe la fecha del evento ' . $existeFecha->fecha . ' vuelva a modificar',
                ];
            } else {
                if ($event->save()) {
                    $response = [
                        'status' => true,
                        'message' => 'Se actualizo el evento ' . $event->nombre,
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'No se puede actualizar el evento',
                    ];
                }
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'No hay datos para procesar',
            ];
        }
        return response()->json($response);
    }

}
