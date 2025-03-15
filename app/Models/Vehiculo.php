<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;
    
    protected $table = 'vehiculos';
    protected $fillable = [
        'usuario_id',
        'tipo_vehiculo_id',
        'numero_chasis',
        'placa',
        'marca',
        'modelo',
        'color',
        'anio',
        'capacidad_tanque_litros',
        'imagen'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class);
    }

    public function registrosCarga()
    {
        return $this->hasMany(RegistroCarga::class);
    }
    
    /**
     * Verifica si el vehículo es un automóvil
     */
    public function esAuto()
    {
        return $this->tipo_vehiculo_id === 1; // ID para "Auto"
    }
    
    /**
     * Verifica si el vehículo es una motocicleta
     */
    public function esMoto()
    {
        return $this->tipo_vehiculo_id === 2; // ID para "Moto"
    }
    
    /**
     * Retorna la cantidad recomendada de carga basada en el tipo de vehículo
     */
    public function getCantidadCargaRecomendada()
    {
        return $this->tipoVehiculo->consumo_promedio_litros;
    }
}