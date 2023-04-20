<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_asistencia extends Model
{
    use HasFactory;

    protected $table = 'tipo_asistencia';
    protected $fillable = ['tipo', 'estado'];
}
