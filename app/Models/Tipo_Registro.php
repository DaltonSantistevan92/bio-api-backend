<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Asistencia;

class Tipo_Registro extends Model
{
    use HasFactory;

    protected $table = "tipos_registros";
    protected $fillable = ['tipo','estado'];
    public $timestamps = false;

    public function asistencia(){
        return $this->hasMany(Asistencia::class);
    }


}
