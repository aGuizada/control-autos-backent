<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auto extends Model
{
    use HasFactory;
    protected $table = 'autos';
    protected $fillable = ['numero_chasis', 'imagen', 'usuario_id'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function registrosCarga()
    {
        return $this->hasMany(RegistroCarga::class);
    }
}
