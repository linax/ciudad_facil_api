<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriaEspacioPublico extends Migration
{
    public function up()
    {
        Schema::create('categoria_espacio_publico', function(Blueprint $table) {
            $table->increments('cep_id');
            $table->string('cep_nombre')->comment('nombre de la categoría');
            $table->integer('cep_orden_despliegue')->unique()->comment('campo para ordenar despliegue en aplicaciones');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('categoria_espacio_publico');
    }
}
