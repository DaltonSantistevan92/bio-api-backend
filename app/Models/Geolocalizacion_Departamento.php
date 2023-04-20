<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Departamento;

class Geolocalizacion_Departamento extends Model
{
    use HasFactory;

    protected $table = 'geolocalizacion_departamento';
    protected $fillable = ['departamento_id','lat','log'];
    public $timestamps = false;

    public function departamento(){
        return $this->belongsTo(Departamento::class);
    }


}
