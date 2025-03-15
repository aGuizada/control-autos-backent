<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateControlDeAutosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabla de roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // 'admin', 'usuario'
            $table->timestamps();
        });
        DB::table('roles')->insert([
            ['nombre' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'user', 'created_at' => now(), 'updated_at' => now()]
        ]);
        
        // Tabla de comunidades
        Schema::create('comunidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });
        DB::table('comunidades')->insert([
            ['nombre' => 'Circuata', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Villa Barriento', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Villa Khora', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Miguillas', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Caniamina', 'created_at' => now(), 'updated_at' => now()]
        ]);
        
        // Tabla de usuarios (sin campos de vehículos)
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('telefono')->nullable();
            $table->unsignedBigInteger('rol_id');
            $table->unsignedBigInteger('comunidad_id');
            $table->timestamps();

            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('comunidad_id')->references('id')->on('comunidades')->onDelete('cascade');
        });
        
        // Tabla de tipos de vehículos
        Schema::create('tipos_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Auto, Moto
            $table->float('consumo_promedio_litros'); // Litros estándar para este tipo de vehículo
            $table->timestamps();
        });

        // Insertar tipos de vehículos básicos
        DB::table('tipos_vehiculo')->insert([
            ['nombre' => 'Auto', 'consumo_promedio_litros' => 40.0, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Moto', 'consumo_promedio_litros' => 15.0, 'created_at' => now(), 'updated_at' => now()]
        ]);
        
        // Tabla de vehículos
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('tipo_vehiculo_id');
            $table->string('numero_chasis')->unique();
            $table->string('placa')->nullable();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('color')->nullable();
            $table->integer('anio')->nullable();
            $table->float('capacidad_tanque_litros')->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('tipo_vehiculo_id')->references('id')->on('tipos_vehiculo');
        });
        
        // Tabla de surtidores
        Schema::create('surtidores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ubicacion')->nullable();
            $table->float('capacidad_total_litros');
            $table->float('combustible_disponible_litros');
            $table->string('tipo_combustible'); // Gasolina, Diesel, etc.
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar un surtidor inicial
        DB::table('surtidores')->insert([
            [
                'nombre' => 'Surtidor Principal', 
                'ubicacion' => 'Entrada principal', 
                'capacidad_total_litros' => 5000.0, 
                'combustible_disponible_litros' => 5000.0, 
                'tipo_combustible' => 'Gasolina',
                'activo' => true,
                'created_at' => now(), 
                'updated_at' => now()
            ]
        ]);

        // Tabla de registros de carga (con más campos)
        Schema::create('registros_carga', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('vehiculo_id');
            $table->unsignedBigInteger('surtidor_id');
            $table->dateTime('fecha_carga');
            $table->float('cantidad_litros');
            $table->string('codigo_qr')->unique();
            $table->boolean('qr_habilitado')->default(true);
            $table->dateTime('fecha_proxima_habilitacion')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('cascade');
            $table->foreign('surtidor_id')->references('id')->on('surtidores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registros_carga');
        Schema::dropIfExists('surtidores');
        Schema::dropIfExists('vehiculos');
        Schema::dropIfExists('tipos_vehiculo');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('comunidades');
        Schema::dropIfExists('roles');
    }
}