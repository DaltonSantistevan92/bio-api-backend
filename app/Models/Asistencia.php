<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\{User,Tipo_Registro,Ubicacion};

class Asistencia extends Model
{
    use HasFactory;

    protected $table = "asistencias";
    protected $fillable = ['user_id','tipo_registro_id','fecha','hora','estado'];
    public $timestamps = false;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function tipo_registro(){
        return $this->belongsTo(Tipo_Registro::class);
    }

    public function ubicacion(){
        return $this->hasMany(Ubicacion::class);
    }
}
