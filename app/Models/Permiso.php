<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\{Menu,Rol};

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'permisos';
    protected $fillable = ['menu_id','rol_id','acceso','guard','estado'];
    public $timestamps = false;

    public function menu(){
        return $this->belongsTo(Menu::class);
    }

    public function rol(){
        return $this->belongsTo(Rol::class);
    }


    
}
