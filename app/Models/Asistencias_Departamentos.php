<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\{Asistencia,Departamento};

class Asistencias_Departamentos extends Model
{
    use HasFactory;

    protected $table = 'asistencias_departamentos';
    protected $fillable = ['asistencia_id','departamento_id'];
    public $timestamps = false;

    public function asistencia(){
        return $this->belongsTo(Asistencia::class);
    }

    public function departamento(){
        return $this->belongsTo(Departamento::class);
    }


}
