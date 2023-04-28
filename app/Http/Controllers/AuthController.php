<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    private $personaCtrl;
    private $permisoCtrl;

    public function __construct()
    {

        $this->personaCtrl = new PersonaController();
        $this->permisoCtrl = new PermisoController();
    }

    public function registro(Request $request)
    { //app movil
        try {
            $requestPersona = collect($request->persona)->all();
            $requestUser = collect($request->usuario)->all();

            $validarPersona = $this->personaCtrl->validatePersona($requestPersona);
            $validarUsuario = $this->validateUser($requestUser);

            if ($validarPersona['status'] && $validarUsuario['status']) {
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
            } else {
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
        } catch (\Throwable$th) {
            $response = ['status' => false, 'message' => 'Error del Servidor'];
            return response()->json($response, 500);
        }
    }

    public function validateUser($request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $messages = [
            'email.required' => 'El campo correo es requerido',
            'email.email' => 'El correo no tiene un formato válido',
            'password.required' => 'El campo contraseña es requerido',
        ];
        return $this->validation($request, $rules, $messages);
    }

    public function validation($request, $rules, $messages)
    {
        $response = ['status' => true, 'message' => 'No hubo errores'];

        $validate = Validator::make($request, $rules, $messages);

        if ($validate->fails()) {
            $response = ['status' => false, 'message' => 'Error de validación', 'error' => $validate->errors()];
        }
        return $response;
    }

    public function login(Request $request)
    { //app movil
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credenciales = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credenciales)) {
                $response = ['status' => false, 'message' => 'El correo o las credenciales son invalidas'];
                return response()->json($response);
            }
        } catch (\Throwable$th) {
            $response = ['status' => false, 'message' => 'Error del Servidor'];
            return response()->json($response);
        }
        //compact('token')

        $user = User::where('email', $request->email)->first();
        $user->rol;
        $user->persona;

        $menu = $this->permisoCtrl->permisosAppMovil($user->rol->id);

        $response = ['status' => true, 'message' => 'Bienvenido', 'user' => $user, 'token' => $token, 'menu' => $menu];
        return response()->json($response);
    }

    public function loginWeb(Request $request)
    { //app web
        $requestUser = collect($request)->all();
        $validarUsuario = $this->validateUser($requestUser);

        if ($validarUsuario['status']) {
            $user = User::where('email', $requestUser['email'])->first();

            if ($user != null) {
                $rolUser = User::where('rol_id', $user->rol_id)->where('rol_id', 1)->orWhere('rol_id', 2)->first();

                if ($rolUser) {
                    $hashPassword = Hash::check($requestUser['password'], $user->password);

                    if ($this->validarCheckPassword($hashPassword, $user->password)) {
                        $user->rol;
                        $user->persona;
                        $menu = $this->permisoCtrl->permisosAppWeb($user->rol->id);
                        $payloadable = ['user' => $user, 'menu' => $menu];

                        //$token = JWTAuth::claims($payloadable)->attempt($requestUser);  //credenciales
                        //$token = JWTAuth::customClaims($payloadable)->fromUser($user);  //user
                        $token = JWTAuth::claims($payloadable)->fromSubject($user); //user

                        $response = [
                            'status' => true,
                            'message' => "Acceso al Sistema Web",
                            'token' => $token,
                        ];
                    } else {
                        $response = ['status' => false, 'message' => "Contraseña Incorrecta"];
                    }
                } else {
                    $response = ['status' => false, 'message' => "No tiene Acceso al Sistema"];
                }
            } else {
                $response = ['status' => false, 'message' => "Correo Incorrecto"];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'No se pudo logear',
                'fails' => [
                    'error_user' => $validarUsuario["error"] ?? "No presenta errores",
                ],
            ];
        }
        return response()->json($response, 200);
    }

    private function validarCheckPassword($hashPassword, $passwordUser)
    {
        if ($hashPassword == $passwordUser) {
            return true;
        } else {
            return false;
        }
    }

    //APP MOVIL JWT - EJEMPLO MODO PRUEBA
    public function register(Request $request)
    {
        $response = [];
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);
        $response = ['user' => $user, 'token' => $token];

        return response()->json($response, 200);
    }

    public function logiin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credenciales = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credenciales)) {
                return response()->json(['error' => 'invalid credentials', 400]);
            }
        } catch (\Throwable$th) {
            return response()->json(['error' => 'no create token', 500]);
        }

        return response()->json(compact('token'));
    }

    //sin utilizar
    /* public function login(Request $request){
try {
$requestUser = collect( $request->usuario )->all();
$validarUsuario = $this->validateUserLogin( $requestUser );
$response = [];

if ($validarUsuario['status']) {
$user = User::where('email', $requestUser['email'])->first();

if ($user != null) {
$hashPassword = Hash::check( $requestUser['password'], $user->password );

if($this->validarCheck( $hashPassword, $user->password )){
$user->rol;
$user->persona;
$token = $user->createToken('API TOKEN')->plainTextToken;

$response = [
'status' => true,
'message' => "Acceso al Sistema",
'data' => [
'user' => $user,
'token' => $token
]
];
}else{
$response = [ 'status' => false, 'message' => "Contraseña Incorrecta" ];
}
}else {
$response = [ 'status' => false, 'message' => "Correo Incorrecto" ];
}
} else {
$response = [
'status' => false,
'message' => 'No se pudo logear :(',
'fails' => [
'error_user' => $validarUsuario["error"] ?? "No presenta errores"
]
];
}
return response()->json( $response, 200 );
} catch (\Throwable $th){
$response = [ 'status' => false, 'message' => 'Error del Servidor' ];
return response()->json( $response, 500 );
}
}

private function validarCheck($password1, $password2)
{
if($password1 == $password2){
return true;
}else{
return false;
}
}

public function validateUserLogin( $request )
{
$rules = [
'email' => 'required|email',
'password' => 'required'
];

$messages = [
'email.required' => 'El campo correo es requerido',
'email.email' => 'El correo no tiene un formato válido',
'password.required' => 'El campo contraseña es requerido',
];
return $this->validation( $request, $rules, $messages );
}
 */

}
