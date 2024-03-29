<?php

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\Geolocalizacion_DepartamentoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\Tipo_RegistroController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\SexoController;
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
Route::post('login', [AuthController::class, 'login']);//appMovil  (super admin- trabajador - administrador)
Route::post('loginWeb', [AuthController::class, 'loginWeb']);//appMovil  (super admin- trabajador - administrador
Route::post('registro', [AuthController::class, 'registro']);
Route::get('sexo/listar', [SexoController::class, 'listarSexo']);


//RUTAS PROTEGIDAS JWT  (Segunda forma)
Route::middleware('jwt.verify')->group( function () {

    //APPMOVIL

    //SEXO
    Route::get('sexo', [SexoController::class, 'listarSexo']);
    
    //ASISTENCIA
    Route::post('asistencia', [AsistenciaController::class, 'registrarAsistencia']);
    Route::get('getDateTime', [AsistenciaController::class, 'getDateTime']);
    Route::get('search/{user_id}', [AsistenciaController::class, 'buscarUltimoTipo']);
    Route::get('buscarUltimoTipoAsistencia/{user_id}', [AsistenciaController::class, 'buscarUltimoTipoAsistencia']);

     
    //REPORTE DE ASISTENCIA
     Route::get('reporteTrabajador/{user_id}/{f_inicio}/{f_fin}/{tipo_asistencia_id}', [AsistenciaController::class, 'reporteTrabajador']);
     Route::get('reporteSuperAdmin/{f_inicio}/{f_fin}/{tipo_asistencia_id}', [AsistenciaController::class, 'reporteSuperAdminAndAdministrador']);

     Route::get('asistenciaXdepartamento/{f_inicio}/{f_fin}/{usuario_id}', [AsistenciaController::class, 'asistenciaXdepartamento']);

    //TIPO ASISTENCIA 
    Route::get('getTipoAsistencia', [AsistenciaController::class, 'cargarTipoAsistencia']);

    //TIPOS
    Route::get('getTipos', [Tipo_RegistroController::class, 'getTipos']);

    //ACTUALIZAR INFORMACION DEL USUARIO
    Route::post('updateDataUser', [UsuarioController::class, 'updateDataUser']);

    Route::post('updatePassword', [UsuarioController::class, 'updatePassword']);


    //APPWEB
    
    //USER
    Route::get('user', [UsuarioController::class, 'getUser']);
    Route::post('updatePerfilUser', [UsuarioController::class, 'updatePerfil']);
    Route::get('deleteUser/{id}', [UsuarioController::class, 'deleteUser']);
    Route::post('createUser', [UsuarioController::class, 'createUser']);
    Route::post('asignUserDepartament', [UsuarioController::class, 'asignarDepartamentoUsuario']);
    Route::get('usersDepart', [UsuarioController::class, 'usersDepart']);

    //Rerportes
    Route::get('listUsuariosXdepartamentos/{departamento_id}', [UsuarioController::class, 'usuariosXdepartamentos']);
    
     //TIPO ROLES
     Route::get('listarRol', [RolController::class, 'listarRol']);

     //EVENTO
     Route::get('event/list', [EventoController::class, 'listarEvento']);
     Route::post('event/save', [EventoController::class, 'guardarEvento']);
     Route::get('event/delete/{id}', [EventoController::class, 'eliminarEvento']);
     Route::post('event/update', [EventoController::class, 'editarEvento']);
    
    
     //DEPARTAMENTO
     Route::get('departament/list', [DepartamentoController::class, 'getAllDepartamentos']);
     Route::get('departament/activeList', [DepartamentoController::class, 'getAllDepartamentosActivos']);
     Route::post('departament/save', [DepartamentoController::class, 'createDepartamento']);
     Route::get('departament/delete/{id}', [DepartamentoController::class, 'deleteDepartamento']);
     Route::post('departament/update', [DepartamentoController::class, 'updateDepartamento']);
     Route::get('departament/listNotAssigned', [DepartamentoController::class, 'getAllUsuariosSinDepartamentos']);
     //count User - Departaments - Events 

     Route::get('count/list', [UsuarioController::class, 'getAllCount']);

     //TENDENCIA DE ASISTENCIA GLOBALES
     Route::get('tendenciaAsistenciaGlobales', [AsistenciaController::class, 'tendeciasAsistenciasGlobal']);
     Route::get('regresionLinealAsistencias/{temporalidad_id}/{tipo_asistencia_id}/{fechaInicio}/{fechaFin}', [AsistenciaController::class, 'regresionLinealAsistencias']);

    //Reportes
    Route::get('horasTrabajadas/{user_id}/{fecha_inicio}/{fecha_fin}', [AsistenciaController::class, 'horasTrabajadas']);
    Route::get('puntualesAtrasadoAsistencia/{fecha_inicio}/{fecha_fin}', [AsistenciaController::class, 'puntalesAtrasadosAsistencia']);

    //KPI
    Route::get('horasExtrasAgrupadosXDepartamentoKpi/{fecha_inicio}/{fecha_fin}', [AsistenciaController::class, 'horasExtrasAgrupadosXDepartamentoKpi']);

    Route::get('obtenerIndiceAtrasoPorDepartamento', [AsistenciaController::class, 'obtenerIndiceAtrasoPorDepartamento']);








    
}); 

Route::get('mostrarImagen/{carpeta}/{archivo}',[ ToolController::class, 'mostrarImagen' ]);
Route::post('subirArchivo',[ ToolController::class, 'subirArchivo' ]);





