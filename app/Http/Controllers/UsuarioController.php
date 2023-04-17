<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona;

class UsuarioController extends Controller
{
    public function updateDataUser(Request $request){
        $requestUser = (object) $request->usuario;
        $requestPerson = (object) $request->persona;
        $response = [];
        
        //Search Id user
        $dataUser = User::find($requestUser->user_id);
        $cargo = $dataUser->rol->cargo;

        if($requestUser){
       
            if($dataUser){
                //Update Data User
                $dataUser->name = $requestUser->name;
                $dataUser->email = $requestUser->email;
                //$dataUser->password = $requestUser->password;

                //Update Data Person
                $dataPerson = Persona::find($requestPerson->persona_id);
                $dataPerson->cedula = $requestPerson->cedula;
                $dataPerson->nombres = $requestPerson->nombres;
                $dataPerson->apellidos = $requestPerson->apellidos;
                $dataPerson->num_celular = $requestPerson->num_celular;
                $dataPerson->direccion = $requestPerson->direccion;
                $dataPerson->save();
                $dataUser->save(); 

                $response = [
                    'status' => true,
                    'message' => 'El '.$cargo .' se ha actualizado correctamente',
                    'data' => $dataUser,
                ];
            }else{
                $response = [
                    'status' => false,
                    'message' => 'Error. No se puede actualizar tu información.'
                ];
            }  
        }else{
            $response = [
                'status' => false,
                'message' => 'Error. No hay datos del '.$cargo
            ];
        }
        return response()->json($response);
    }
}
