<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RegistroCarga extends Model
{
    use HasFactory;

    protected $table = 'registros_carga';
    
    protected $fillable = [
        'usuario_id',
        'vehiculo_id',
        'surtidor_id',
        'fecha_carga',
        'cantidad_litros',
        'codigo_qr',
        'qr_habilitado',
        'fecha_proxima_habilitacion'
    ];

    protected $casts = [
        'fecha_carga' => 'datetime',
        'fecha_proxima_habilitacion' => 'datetime',
        'qr_habilitado' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function surtidor()
    {
        return $this->belongsTo(Surtidor::class);
    }
    
    /**
     * Genera un código QR único para este registro
     */
    public function generarCodigoQR()
    {
        $this->codigo_qr = 'QR-' . uniqid() . '-' . $this->usuario_id . '-' . $this->vehiculo_id;
        return $this->codigo_qr;
    }
    
    /**
     * Deshabilita el código QR para futuras cargas
     * y establece la fecha para su próxima habilitación
     */
    public function deshabilitarQR($diasEspera = 5)
    {
        $this->qr_habilitado = false;
        $this->fecha_proxima_habilitacion = Carbon::now()->addDays($diasEspera);
        return $this->save();
    }
    
    /**
     * Habilita el código QR manualmente
     */
    public function habilitarQR()
    {
        $this->qr_habilitado = true;
        $this->fecha_proxima_habilitacion = null;
        return $this->save();
    }
    
    /**
     * Verifica si el QR está habilitado para su uso
     */
    public function estaHabilitado()
    {
        if ($this->qr_habilitado) {
            return true;
        }
        
        // Si ya pasó la fecha de próxima habilitación, habilitarlo automáticamente
        if ($this->fecha_proxima_habilitacion && 
            Carbon::now()->greaterThanOrEqualTo($this->fecha_proxima_habilitacion)) {
            return $this->habilitarQR();
        }
        
        return false;
    }
    
    /**
     * Crea un nuevo registro de carga y actualiza el inventario del surtidor
     */
    public static function registrarCarga($usuarioId, $vehiculoId, $surtidorId, $cantidadLitros)
    {
        // Verificar que el surtidor tenga suficiente combustible
        $surtidor = Surtidor::findOrFail($surtidorId);
        
        if (!$surtidor->haySuficienteCombustible($cantidadLitros)) {
            return [
                'success' => false,
                'mensaje' => 'No hay suficiente combustible en el surtidor'
            ];
        }
        
        // Crear el registro de carga
        $registro = new self();
        $registro->usuario_id = $usuarioId;
        $registro->vehiculo_id = $vehiculoId;
        $registro->surtidor_id = $surtidorId;
        $registro->fecha_carga = Carbon::now();
        $registro->cantidad_litros = $cantidadLitros;
        $registro->generarCodigoQR();
        $registro->qr_habilitado = true;
        $registro->save();
        
        // Reducir el combustible del surtidor
        $surtidor->reducirCombustible($cantidadLitros);
        
        return [
            'success' => true,
            'registro' => $registro,
            'codigo_qr' => $registro->codigo_qr
        ];
    }
    
    /**
     * Escanea un código QR y lo desactiva
     */
    public static function escanearQR($codigoQR)
    {
        $registro = self::where('codigo_qr', $codigoQR)->first();
        
        if (!$registro) {
            return [
                'success' => false,
                'mensaje' => 'Código QR no encontrado'
            ];
        }
        
        if (!$registro->estaHabilitado()) {
            return [
                'success' => false,
                'mensaje' => 'Código QR no habilitado',
                'fecha_proxima_habilitacion' => $registro->fecha_proxima_habilitacion
            ];
        }
        
        // Deshabilitar el QR después de usarlo
        $registro->deshabilitarQR();
        
        return [
            'success' => true,
            'mensaje' => 'Carga autorizada',
            'registro' => $registro,
            'fecha_proxima_habilitacion' => $registro->fecha_proxima_habilitacion
        ];
    }
}