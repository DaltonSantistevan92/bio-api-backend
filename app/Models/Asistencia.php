<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\{User,Tipo_Registro,Ubicacion,Tipo_Asistencia, Asistencias_Departamentos};

class Asistencia extends Model
{
    use HasFactory;

    protected $table = "asistencias";
    protected $fillable = ['user_id','tipo_asistencia_id','tipo_registro_id','fecha','hora','estado'];
    public $timestamps = false;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function tipo_asistencia(){
        return $this->belongsTo(Tipo_Asistencia::class);
    }

    public function tipo_registro(){//muchoss a uno
        return $this->belongsTo(Tipo_Registro::class);
    }

    public function ubicacion(){//uno a muchos
        return $this->hasMany(Ubicacion::class);
    }

    public function asistencias_departamento(){
        return $this->hasMany(Asistencias_Departamentos::class);
    }
}
