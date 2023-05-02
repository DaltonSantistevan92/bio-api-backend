<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash};


class UsuarioController extends Controller
{
    
    public function __construct()
    {

    }

    public function updatePassword(Request $request)
    {
        $requestUser = (object) $request->usuario;

        //Search User ID
        $dataUser = User::find($requestUser->user_id);
        $response = [];

        if ($dataUser) {
            //Password update
            $encriptarPassword = Hash::make($requestUser->password);
            $dataUser->password = $encriptarPassword;

            if ($dataUser->save()) {
                $response = [
                    'status' => true,
                    'message' => 'Tu contraseña ha sido actualizada con éxito.',
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Error. No se puede actualizar tu contraseña.',
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

    private function savePerson($data)
    {
        $existeCedula = Persona::where('cedula', $data->cedula)->get()->first();
        $response = [];

        if ($existeCedula) {
            $response = ['status' => false, 'message' => 'La cédula ya existe'];
        } else {
            $newPersona = new Persona();
            $newPersona->cedula = $data->cedula;
            $newPersona->nombres = strtolower($data->nombres);
            $newPersona->apellidos = strtolower($data->apellidos);
            $newPersona->num_celular = $data->num_celular;
            $newPersona->direccion = strtolower($data->direccion);
            $newPersona->estado = 'A';

            if ($newPersona->save()) {
                $response = ['status' => true, 'message' => 'Se registro con exito', 'persona' => $newPersona];
            } else {
                $response = ['status' => false, 'message' => 'Error. No se pudo registrar la persona'];
            }
        }
        
        return $response;
    }
 
    public function createUser(Request $request)
    {
        $requestUser = (object) $request->usuario;
        $requestPerson = (object) $request->persona;
        $response = [];

        $responsePerson = $this->savePerson($requestPerson);

        if ($responsePerson['status'] == true) {
            $id_person = $responsePerson['persona']->id;
            $encriptarPassword = Hash::make($requestUser->password);

            $user = User::create([
                'persona_id' => $id_person,
                'rol_id' => $requestUser->rol_id,
                'name' => strtolower($requestUser->name),
                'email' => $requestUser->email,
                'password' => $encriptarPassword,
                'imagen' => $requestUser->imagen,
                'estado' => 'A'
            ]);

            $response = ['status' => true, 'message' => "El usuario se registro con exito", 'data' => $user];

        } else {
            $response = ['status' => false, 'message' => $responsePerson['message']];
        }

        return response()->json($response);
    } 

    

    public function deleteUser($user_id)
    {
        $dataUser = User::find(intval($user_id));
        $response = [];

        if ($dataUser) {
            $dataUser->estado = "I";
            if ($dataUser->save()) {
                $response = [
                    'status' => true,
                    'message' => 'El usuario ha sido eliminado correctamente',
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Error, No se ha podido dar de baja este usuario.',
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Error. No hay datos de este usuario',
            ];

        }

        return response()->json($response);
    }

    public function updateDataUser(Request $request)
    {
        $requestUser = (object) $request->usuario;
        $requestPerson = (object) $request->persona;
        $response = [];

        //Search Id user
        $dataUser = User::find($requestUser->user_id);
        $cargo = $dataUser->rol->cargo;

        if ($requestUser) {

            if ($dataUser) {
                //Update Data User
                $dataUser->rol_id = $requestUser->rol_id;
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

                $dataUser->persona;
                $dataUser->rol;

                $response = [
                    'status' => true,
                    'message' => 'El ' . $cargo . ' se ha actualizado correctamente',
                    'data' => $dataUser,
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Error. No se puede actualizar tu información.',
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Error. No hay datos del ' . $cargo,
            ];
        }
        return response()->json($response);
    }

    public function getUser()
    {
        $usuarios = User::where('estado', 'A')->where('id','<>',3)->get();
        $response = [];

        if ($usuarios->count() > 0) {
            foreach ($usuarios as $item) {
                $item->persona;
                $item->rol;
            }

            $response = ['status' => true, 'message' => 'existen datos', 'data' => $usuarios];
        } else {
            $response = ['status' => true, 'message' => 'no existen datos', 'data' => null];
        }

        return response()->json($response, 200);
    }
}
