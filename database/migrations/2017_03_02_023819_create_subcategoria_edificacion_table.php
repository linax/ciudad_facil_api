<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcategoriaEdificacionTable extends Migration
{

    public function up()
    {
        Schema::create('subcategoria_edificacion', function(Blueprint $table) {
            $table->increments('sub_id');
            $table->integer('sub_cat_id')->unsigned()->comment('identificador de categoría padre');
            $table->string('sub_nombre')->comment('nombre de la subcategoría');
            $table->integer('sub_orden')->comment('orden de despliegue');
            $table->timestamps();
            $table->softDeletes();
            // Schema declaration
            // Constraints declaration
            $table->foreign('sub_cat_id')
                    ->references('cat_id')
                    ->on('categoria_edificacion');
        });
    }

    public function down()
    {
        Schema::drop('subcategoria_edificacion');
    }
}
