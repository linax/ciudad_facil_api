<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemEvaluarTable extends Migration
{

    public function up()
    {
        Schema::create('item_evaluar', function(Blueprint $table) {
            $table->increments('iev_id');
            $table->string('iev_descripcion')->comment('Texto que describe el campo, por ejemplo: Accesibilidad exterior');
            $table->timestamps();
            $table->softDeletes();
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('item_evaluar');
    }
}
