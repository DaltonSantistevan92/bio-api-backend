<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\{Geolocalizacion_Departamento, Asistencias_Departamentos,User };

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';
    protected $fillable = ['nombre','estado'];
    public $timestamps = false;

    public function geolocalizacion_departamento(){
        return $this->hasMany(Geolocalizacion_Departamento::class);
    }

    public function asistencias_departamento(){
        return $this->hasMany(Asistencias_Departamentos::class);
    }

    public function user(){
        return $this->hasMany(User::class);
    }

 
    
}
