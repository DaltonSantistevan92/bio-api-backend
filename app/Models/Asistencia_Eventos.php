<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Asistencia,Evento};

class Asistencia_Eventos extends Model
{
    use HasFactory;

    protected $table = 'asistencias_eventos';
    protected $fillable = ['asistencia_id','evento_id'];
    public $timestamps = false;


    public function asistencia(){
        return $this->belongsTo(Asistencia::class);
    }

    public function evento(){
        return $this->belongsTo(Evento::class);
    }


}
