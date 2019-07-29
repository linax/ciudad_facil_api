<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuariosTable extends Migration
{

    public function up()
    {
        Schema::create('usuario', function(Blueprint $table) {
            $table->increments('usu_id');
            $table->string('usu_email')->comment('correo electrónico del usuario');
            $table->string('usu_nombre_usuario')->comment('nombre de usuario para login (en caso de ser necesario)');
            $table->string('usu_password')->nullable()->comment('password de login a app');
            $table->string('usu_facebook_id')->nullable()->comment('identificador login facebook');
            $table->string('usu_twitter_id')->nullable()->comment('identificador login twitter');
            $table->string('usu_nombres')->nullable()->comment('nombres de la persona usuario');
            $table->string('usu_apellido_paterno')->nullable()->comment('apellido paterno usuario');
            $table->string('usu_apellido_materno')->nullable()->comment('apellido materno usuario');
            $table->boolean('usu_discapacidad')->nullable()->comment('flag para identificar si el usuario cuenta con discapacidad');
            $table->tinyinteger('usu_tipo_discapacidad')->nullable()->comment('Tipo de discapacidad, nombrar tipos:');
            $table->timestamps();
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('usuario');
    }
}
