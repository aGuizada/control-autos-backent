<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        // Tabla de usuarios
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('telefono')->nullable();
            $table->unsignedBigInteger('rol_id');
            $table->unsignedBigInteger('comunidad_id');

            // Campos que estaban en la tabla autos
            $table->string('numero_chasis')->unique();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('imagen')->nullable();

            $table->timestamps();

            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('comunidad_id')->references('id')->on('comunidades')->onDelete('cascade');
        });
        

        // Tabla de registros de carga
        Schema::create('registros_carga', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id'); // Ahora referencia a usuarios
            $table->dateTime('fecha_carga');
            $table->boolean('qrHabilitado')->default(true); // Agregar el campo qrHabilitado
            $table->timestamps();
        
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
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
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('comunidades');
        Schema::dropIfExists('roles');
    }
}
