<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\{Validator,Hash};

class UsuarioController extends Controller
{

    public function updatePassword(Request $request){
        $requestUser = (object) $request->usuario;

        //Search User ID
        $dataUser = User::find($requestUser->user_id);
        $response = [];

        if($dataUser){
            //Password update
            $encriptarPassword = Hash::make($requestUser->password);
            $dataUser->password = $encriptarPassword;

            if($dataUser->save()){
                $response = [
                    'status' => true,
                    'message' => 'Tu contraseña ha sido actualizada con éxito.'
                ];
            }else{
                $response = [
                    'status' => false,
                    'message' => 'Error. No se puede actualizar tu contraseña.'
                ];
            }
        }else{
            $response = [
                'status' => false,
                'message' => 'No hay datos para procesar'
            ];
        }

        
        return response()->json($response);
    }
    

    public function updateDataUser(Request $request){
        $requestUser = (object) $request->usuario;
        $requestPerson = (object) $request->persona;
        $response = [];

        $cedula = $requestPerson->cedula;
        
        //Search Id user
        $dataUser = User::find($requestUser->user_id);
        $cargo = $dataUser->rol->cargo;

        if($requestUser){

            /* $existeCedula = Persona::where('cedula',$cedula)->get()->first();

            if ($existeCedula) {
                $response = [
                    'status' => false,
                    'message' => 'La cédula ya existe',
                ];
            }else { */
                if($dataUser){
                    //Update Data User
                    $dataUser->name = $requestUser->name;
                    $dataUser->email = $requestUser->email;
                    $dataUser->imagen = $requestUser->imagen;
                    
                    //Update Data Person
                    $dataPerson = Persona::find($requestPerson->persona_id);
                    $dataPerson->cedula = $requestPerson->cedula;
                    $dataPerson->nombres = $requestPerson->nombres;
                    $dataPerson->apellidos = $requestPerson->apellidos;
                    $dataPerson->num_celular = $requestPerson->num_celular;
                    $dataPerson->direccion = $requestPerson->direccion;
                    $dataPerson->save();
                    $dataUser->save(); 
    
                    $dataUser->persona;    $dataUser->rol;
    
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
           /*  }  */
        }else{
            $response = [
                'status' => false,
                'message' => 'Error. No hay datos del '.$cargo
            ];
        }
        return response()->json($response);
    }
   
}
