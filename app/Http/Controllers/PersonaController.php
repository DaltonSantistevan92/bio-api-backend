<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator,Hash};

use App\Models\Persona;

class PersonaController extends Controller
{
    

    public function validatePersona( $request )
    {
        $rules = ['nombres' => 'required'];

        $messages = ['nombres.required' => 'El nombre es requerido'];

        return $this->validation( $request, $rules, $messages );
    }

    public function validation( $request, $rules, $messages )
    {
        
        $response = [ 'status' => true, 'message' => 'No hubo errores' ];
        
        $validate = Validator::make( $request, $rules, $messages ); 
       
        if ( $validate->fails() ) {
            $response = [ 'status' => false, 'message' => 'Error de validación', 'error' => $validate->errors() ];
        }
        return $response;
    }

    public function guardarPersona(  $data ){
        $response = [];

        if (count($data) > 0 ) {
            $newPersona = new Persona();
            $newPersona->nombres = $data['nombres'];
            $newPersona->estado = 'A';
            $newPersona->sexo_id = $data['sexo_id'];

            if ($newPersona->save()) {
                $response = [ 'status'=> true, 'message' => 'Se registro con exito', 'persona' => $newPersona ];
            }else{
                $response = [ 'status'=> false, 'message' => 'No se pudo registrar'];
            }
        } else {
            $response = [ 'status'=> false, 'message' => 'No existe data'];
        }
        return $response;
    }
}
