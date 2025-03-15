<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasApiTokens, HasFactory;
    
    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'telefono',
        'rol_id',
        'comunidad_id'
    ];

    protected $hidden = ['password'];

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class);
    }
    
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }
    
    public function registrosCarga()
    {
        return $this->hasMany(RegistroCarga::class);
    }
    
    public function ultimaCarga()
    {
        return $this->registrosCarga()->latest('fecha_carga')->first();
    }
    
    public function esAdmin()
    {
        return $this->rol_id === 1; // ID para el rol "admin"
    }
    
    public function perfil()
    {
        $datos = [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'rol' => $this->rol->nombre,
            'comunidad' => $this->comunidad->nombre
        ];
        
        // Obtener vehículos
        $vehiculos = [];
        foreach ($this->vehiculos as $vehiculo) {
            $vehiculos[] = [
                'id' => $vehiculo->id,
                'tipo' => $vehiculo->tipoVehiculo->nombre,
                'numero_chasis' => $vehiculo->numero_chasis,
                'placa' => $vehiculo->placa,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                'imagen' => $vehiculo->imagen
            ];
        }
        
        $datos['vehiculos'] = $vehiculos;
        
        // Agregar información de última carga
        $ultimaCarga = $this->ultimaCarga();
        if ($ultimaCarga) {
            $datos['ultima_carga'] = [
                'fecha' => $ultimaCarga->fecha_carga->format('Y-m-d H:i:s'),
                'cantidad_litros' => $ultimaCarga->cantidad_litros,
                'vehiculo' => $ultimaCarga->vehiculo->marca . ' ' . $ultimaCarga->vehiculo->modelo,
                'qr_habilitado' => $ultimaCarga->qr_habilitado,
                'proxima_habilitacion' => $ultimaCarga->fecha_proxima_habilitacion 
                    ? $ultimaCarga->fecha_proxima_habilitacion->format('Y-m-d H:i:s') 
                    : null
            ];
        }
        
        return $datos;
    }
}