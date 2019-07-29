<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriaEdificacionTable extends Migration
{

    public function up()
    {
        Schema::create('categoria_edificacion', function(Blueprint $table) {
            $table->increments('cat_id');
            $table->string('cat_nombre')->comment('nombre de la categoría');
            $table->integer('cat_orden_despliegue')->unique()->comment('campo para ordenar despliegue en aplicaciones');
            $table->timestamps();
            $table->softDeletes();
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('categoria_edificacion');
    }
}
