<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroCarga extends Model
{
    use HasFactory;
    protected $table = 'registros_carga';
    protected $fillable = ['auto_id', 'fecha_carga'];

    public function auto()
    {
        return $this->belongsTo(Auto::class);
    }
}
