<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComunasTable extends Migration
{

    public function up()
    {
        Schema::create('comuna', function(Blueprint $table) {
            $table->increments('com_id');
            $table->integer('com_pro_id')->unsigned()->comment('identificador a la provincia a la que pertenece la comuna');
            $table->string('com_nombre')->comment('nombre de la comuna');
            $table->integer('com_orden')->comment('orden de despliegue de comuna');
            $table->timestamps();

            $table->foreign('com_pro_id')
                ->references('pro_id')
                ->on('provincia');
        });
    }

    public function down()
    {
        Schema::drop('comuna');
    }
}
