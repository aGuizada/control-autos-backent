<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surtidor extends Model
{
    use HasFactory;
    
    protected $table = 'surtidores';
    protected $fillable = [
        'nombre',
        'ubicacion',
        'capacidad_total_litros',
        'combustible_disponible_litros',
        'tipo_combustible',
        'activo'
    ];

    public function registrosCarga()
    {
        return $this->hasMany(RegistroCarga::class);
    }
    
    /**
     * Reduce la cantidad de combustible disponible en el surtidor
     * 
     * @param float $cantidad Cantidad de litros a reducir
     * @return bool Si la operación fue exitosa
     */
    public function reducirCombustible($cantidad)
    {
        if ($this->combustible_disponible_litros >= $cantidad) {
            $this->combustible_disponible_litros -= $cantidad;
            return $this->save();
        }
        return false;
    }
    
    /**
     * Recarga el combustible del surtidor
     * 
     * @param float $cantidad Cantidad de litros a añadir
     * @return bool Si la operación fue exitosa
     */
    public function recargarCombustible($cantidad)
    {
        $nuevoTotal = $this->combustible_disponible_litros + $cantidad;
        
        if ($nuevoTotal <= $this->capacidad_total_litros) {
            $this->combustible_disponible_litros = $nuevoTotal;
            return $this->save();
        }
        return false;
    }
    
    /**
     * Comprueba si hay suficiente combustible para realizar una carga
     * 
     * @param float $cantidad Cantidad de litros requeridos
     * @return bool Si hay suficiente combustible
     */
    public function haySuficienteCombustible($cantidad)
    {
        return $this->activo && $this->combustible_disponible_litros >= $cantidad;
    }
}