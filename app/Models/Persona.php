<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';
    protected $fillable = ['cedula','nombres','apellidos','num_celular','direccion','estado'];
    public $timestamps = false;

    public function user(){
        return $this->hasMany(User::class);
    }
}
