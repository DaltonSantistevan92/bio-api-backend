<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Persona,Departamento};

class Trabajador extends Model
{
    use HasFactory;
    protected $table = "trabajadores";
    protected $fillable = ['persona_id','status'];
    public $timestamps = false;

    public function persona(){
        return $this->belongsTo(Persona::class);
    }

   
}
