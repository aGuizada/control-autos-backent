<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroCarga extends Model
{
    use HasFactory;

    protected $table = 'registros_carga';

    protected $fillable = ['usuario_id', 'fecha_carga', 'qrHabilitado']; // Asegúrate de agregar qrHabilitado aquí


    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
