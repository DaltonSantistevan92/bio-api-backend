<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;

class EventoController extends Controller
{

    public function buscarEventos($fecha){
        $response = [];
        $event = Evento::whereDate('fecha',$fecha)->get()->first();

        if ($event) {
            $response = [
                'status' => true,
                'evento_id' => $event->id,
                'message' => 'existe evento'
            ];   
        }else {
            $response = [
                'status' => false,
                'message' => 'No hay eventos disponibles'
            ];
        }
        return $response;
    }

}
