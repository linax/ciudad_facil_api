<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriaEspacioPublicoTable extends Migration
{

    public function up()
    {
        Schema::create('categoria_espacio_publico', function(Blueprint $table) {
            $table->increments('ces_id');
            $table->string('ces_nombre')->comment('nombre de la categoría');
            $table->integer('ces_orden_despliegue')->unique()->comment('campo para ordenar despliegue en aplicaciones');
            $table->timestamps();
            $table->softDeletes();
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('categoria_espacio_publico');
    }
}
