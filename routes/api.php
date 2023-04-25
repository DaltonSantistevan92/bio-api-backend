<?php

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Geolocalizacion_DepartamentoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\Tipo_RegistroController;
use App\Http\Controllers\ToolController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

//RUTAS PROTEGIDAS JWT  (Primera forma)
/* Route::group( ['middleware' => ['jwt.verify'] ], function (){
    Route::get('rol/listar', [RolController::class, 'listarRol']);

}); */

/* //RUTAS PROTEGIDAS SANCTUM
Route::group( ['middleware' => ['auth:sanctum'] ], function (){

    Route::get('rol/listar', [RolController::class, 'listarRol']);
}); */




//RUTAS PUBLICAS
Route::post('login', [AuthController::class, 'login']);//appMovil
Route::post('registro', [AuthController::class, 'registro']);


//RUTAS PROTEGIDAS JWT  (Segunda forma)
Route::middleware('jwt.verify')->group( function () {

    //ROL
    Route::get('rol/listar', [RolController::class, 'listarRol']);
    
    //ASISTENCIA
    Route::post('asistencia', [AsistenciaController::class, 'registrarAsistencia']);
    Route::get('getDateTime', [AsistenciaController::class, 'getDateTime']);

    

    Route::get('search/{user_id}', [AsistenciaController::class, 'buscarUltimoTipo']);
    Route::get('buscarUltimoTipoAsistencia/{user_id}', [AsistenciaController::class, 'buscarUltimoTipoAsistencia']);


     
    //REPORTE DE ASISTENCIA
     Route::get('reporte/{user_id}/{f_inicio}/{f_fin}/{tipo_asistencia_id}', [AsistenciaController::class, 'reporte']);

    //TIPO ASISTENCIA
    Route::get('getTipoAsistencia', [AsistenciaController::class, 'cargarTipoAsistencia']);

    //TIPOS
    Route::get('getTipos', [Tipo_RegistroController::class, 'getTipos']);

    //ACTUALIZAR INFORMACION DEL USUARIO
    Route::post('updateDataUser', [UsuarioController::class, 'updateDataUser']);

    Route::post('updatePassword', [UsuarioController::class, 'updatePassword']);


    
}); 

Route::get('mostrarImagen/{carpeta}/{archivo}',[ ToolController::class, 'mostrarImagen' ]);
Route::post('subirArchivo',[ ToolController::class, 'subirArchivo' ]);





