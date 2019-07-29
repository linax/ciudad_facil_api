<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvinciasTable extends Migration
{

    public function up()
    {
        Schema::create('provincia', function(Blueprint $table) {
            $table->increments('pro_id');
            $table->integer('pro_reg_id')->unsigned()->comment('identificador de región a la que pertenece la provincia');
            $table->string('pro_nombre')->comment('nombre de provincia');
            $table->integer('pro_orden')->comment('orden de listado de provincia');
            $table->timestamps();
            
            $table->foreign('pro_reg_id')
                ->references('reg_id')
                ->on('region');
        });
    }

    public function down()
    {
        Schema::drop('provincia');
    }
}
