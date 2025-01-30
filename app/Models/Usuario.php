<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;
    protected $table = 'usuarios';
    protected $fillable = ['nombre', 'email', 'password', 'telefono', 'rol_id', 'comunidad_id'];

    protected $hidden = ['password'];

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class);
    }

    public function autos()
    {
        return $this->hasMany(Auto::class);
    }
}
