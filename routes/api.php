<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolController;
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

}); 






