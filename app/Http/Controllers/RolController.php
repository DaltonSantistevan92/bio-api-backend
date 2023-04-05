<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function listarRol(){
        $response = [];
        $roles = Rol::all();

        if ($roles->count() > 0) {
            $response = [ 'status' => true, 'message' => 'existen roles', 'data' => $roles ];
        }else{
            $response = [ 'status' => false, 'message' => 'no existen roles', 'data' => null ];
        }
        return response()->json($response, 200);
    }


}
