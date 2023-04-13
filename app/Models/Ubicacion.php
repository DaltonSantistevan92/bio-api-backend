<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Asistencia;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = "ubicaciones";
    protected $fillable = ['asistencia_id','latitud','longitud'];
    public $timestamps = false;

    public function asistencia(){
        return $this->belongsTo(Asistencia::class);
    }
}
