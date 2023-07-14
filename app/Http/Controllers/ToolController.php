<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;


class ToolController extends Controller
{
    //

    public function mostrarImagen($carpeta,$archivo){
       /*  try { */
            $existeArchivo = Storage::disk($carpeta)->exists($archivo);
            $response = [];

            if($existeArchivo){
                $file = Storage::disk($carpeta)->get($archivo);
                return new Response($file,200);
            }else{
                $response=[
                    'status' => false,
                    'mensaje' => 'No existe la imagen',
                    'imagen' => null
                ];
            }
            return response()->json($response);
        /* } catch (\Throwable $th) {
            $response = [ 'status' => false, 'message' => 'Error del Servidor' ];
            return response()->json( $response, 500 );
        } */
    }

    public function subirArchivo(Request $request){
        if($request->hasFile('img_user-0')){
            $imagen = $request->file('img_user-0');
            $filenamewithextension = $imagen->getClientOriginalName();   //Archivo con su extension            
            Storage::disk('usuarios')->put($filenamewithextension, fopen($request->file('img_user-0'), 'r+'));
            
            $response = [
                'status' => true,
                'imagen' => $filenamewithextension,
                'mensaje' => 'La imagen se ha subido al servidor'
            ];
        }else{
            $response = [
                'status' => false,
                'imagen' => '',
                'mensaje' => 'No hay un archivo para procesar'
            ];
        }
        return response()->json($response);
    }


}
