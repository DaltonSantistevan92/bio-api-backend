<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Asistencia_Eventos;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';
    protected $fillable = ['nombre','fecha','estado'];
    public $timestamps = false;

    public function asistencia_evento(){
        return $this->hasMany(Asistencia_Eventos::class);
    }

    

}
