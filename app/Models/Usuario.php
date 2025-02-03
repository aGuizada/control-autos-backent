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
        'comunidad_id',
        'numero_chasis', // Campo del auto
        'marca',         // Campo del auto
        'modelo',        // Campo del auto
        'imagen'         // Campo del auto
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
    public function perfil()
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'numero_chasis' => $this->numero_chasis,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'imagen' => $this->imagen,
            'rol' => $this->rol->nombre,  // O cualquier atributo del rol que necesites
            'comunidad' => $this->comunidad->nombre  // O cualquier atributo de la comunidad
        ];
    }
}
