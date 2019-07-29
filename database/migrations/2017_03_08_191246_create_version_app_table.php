<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionAppTable extends Migration
{

    public function up()
    {
        Schema::create('version_app', function(Blueprint $table) {
            $table->increments('ver_id');
            $table->string('ver_version_app')->comment('String de la versión, ejemplo: 1.2');
            $table->string('ver_especificaciones')->nullable()->comment('Especificaciones de la versión');
            $table->date('ver_fecha_app')->comment('Fecha lanzamiento de la aplicación');
            $table->timestamps();
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('version_app');
    }
}
