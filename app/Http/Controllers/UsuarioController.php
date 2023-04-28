<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\{Validator,Hash};
use PhpParser\Node\Stmt\TryCatch;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsuarioController extends Controller
{
    private $personaCtrl;


    public function __construct()
    {

        $this->personaCtrl = new PersonaController();
        
    }

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


    public function createUser(Request $request){

        try {
            $requestPersona = collect($request->persona)->all();
            $requestUser = collect($request->usuario)->all();

            $validarPersona = $this->personaCtrl->validatePersona($requestPersona);
            $validarUsuario = $this->validateUser($requestUser);

            if($validarPersona['status'] && $validarUsuario['status']){
                $responsePersona = $this->personaCtrl->guardarPersona($requestPersona);
                $persona_id = $responsePersona['persona']->id;
                $encriptarPassword = Hash::make($requestUser['password']);

                $user = User::create([
                    'persona_id' => $persona_id,
                    'rol_id' => 4,
                    'email' => $requestUser['email'],
                    'password' => $encriptarPassword,
                    'imagen' => 'user-default.jpg',
                ]);

                $token = JWTAuth::fromUser($user);

                $response = ['status' => true, 'message' => "El usuario se registro con exito"]; //'usuario' => ['user' => $user, 'token' => $token ]

            }else{

                $response = [
                    'status' => false,
                    'message' => 'No se pudo crear el usuario',
                    'falla' => [
                        'error_persona' => $validarPersona['error'] ?? 'No presenta errores',
                        'error_usuario' => $validarUsuario['error'] ?? 'No presenta errores',
                    ],
                ];

            }

            return response()->json($response, 200);
            
        } catch (\Throwable $th) {
            //throw $th;
            $response = ['status' => false, 'message' => 'Error del Servidor'];
            return response()->json($response, 500);
        }



        
    }

    public function deleteUser($user_id){

        $dataUser = User::find(intval($user_id));
        $response = [];

        if($dataUser){
            $dataUser->estado = "I";
            if($dataUser->save()){
                $response = [
                    'status' => true,
                    'message' => 'El usuario ha sido eliminado correctamente',                
                ];
            }else{
                $response = [
                    'status' => false,
                    'message' => 'Error, No se ha podido dar de baja este usuario.',                
                ];
            }        
        }else{
            $response = [
                'status' => false,
                'message' => 'Error. No hay datos de este usuario',                
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

    public function getUser(){
        $usuarios = User::where('estado','A')->get();
        $response = [];

        if ($usuarios->count() > 0) {
            foreach($usuarios as $item){
                $item->persona;
                $item->rol;
            }

            $response = [ 'status' => true, 'message' => 'existen datos', 'data' => $usuarios ];
        } else {
            $response = [ 'status' => true, 'message' => 'no existen datos', 'data' => null ]; 
        }

        return response()->json($response,200);
    }
}
