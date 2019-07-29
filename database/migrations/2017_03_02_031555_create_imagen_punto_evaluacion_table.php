<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagenPuntoEvaluacionTable extends Migration
{

    public function up()
    {
        Schema::create('imagen_punto_evaluacion', function(Blueprint $table) {
            $table->increments('ima_id')->comment('Clave primaria de la imágen de la edificación'); 
            $table->integer('ima_pev_id')->unsigned()->comment('Clave foránea a la edificación'); 
            $table->string('ima_imagen')->comment('Ruta de la imagen'); 
            $table->timestamps();
            // Schema declaration
            // Constraints declaration

            $table->foreign('ima_pev_id')
                    ->references('pev_id')
                    ->on('punto_evaluacion');
        });
    }

    public function down()
    {
        Schema::drop('imagen_punto_evaluacion');
    }
}
