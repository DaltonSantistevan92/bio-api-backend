<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Persona;

class Sexo extends Model
{
    use HasFactory;
    protected $table = "sexo";
    protected $fillable = ['detalle','status'];
    public $timestamps = false;

    public function persona(){
        return $this->hasMany(Persona::class);
    }
}
