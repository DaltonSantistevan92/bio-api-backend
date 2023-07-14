<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;
    protected $table = "configuraciones";
    protected $fillable = ['hora_entrada','hora_salida','hora_atraso'];
    public $timestamps = false;
}
