<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carlos extends Model
{
    use HasFactory;
   
    public function hola(){
        return 'hola';
    }

    public function cargarModelo(){
        return 'modeloCargado';
    }

    public function main_principal(){
        return "Retorna el main principal";
    }
}
