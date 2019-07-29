<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionsTable extends Migration
{

    public function up()
    {
        Schema::create('region', function(Blueprint $table) {
            $table->increments('reg_id');
            $table->string('reg_nombre')->comment('nombre de la región');
            $table->integer('reg_orden')->comment('orden de despliegue en lista');
            $table->timestamps();

            // Constraints declaration
        });
    }

    public function down()
    {
        Schema::drop('region');
    }
}
