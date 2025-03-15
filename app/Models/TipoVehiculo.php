<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    use HasFactory;
    
    protected $table = 'tipos_vehiculo';
    protected $fillable = [
        'nombre',
        'consumo_promedio_litros'
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }
}