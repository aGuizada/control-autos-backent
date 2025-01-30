<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles'; // Asegura que use la tabla correcta
    protected $fillable = ['nombre']; // Campos que se pueden llenar

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol_id');
    }
}
