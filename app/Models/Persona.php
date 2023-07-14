<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\{User,Sexo,Trabajador};

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';
    protected $fillable = ['cedula','nombres','apellidos','num_celular','direccion','estado','sexo_id'];
    public $timestamps = false;

    public function user(){
        return $this->hasMany(User::class);
    }

    public function sexo(){
        return $this->belongsTo(Sexo::class);
    }

    public function trabajador(){
        return $this->hasMany(Trabajador::class);
    }
}
