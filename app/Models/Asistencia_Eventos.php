<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asistencia;
use App\Models\Evemto;

class Asistencia_Eventos extends Model
{
    use HasFactory;

    protected $table = 'asistencias_eventos';
    protected $fillable = ['asistencia_id','evento_id'];
    public $timestamps = false;


    public function Asistencias(){
        return $this->belongsTo(Asistencia::class);
    }

    public function Eventos(){
        return $this->belongsTo(Evento::class);
    }


}
