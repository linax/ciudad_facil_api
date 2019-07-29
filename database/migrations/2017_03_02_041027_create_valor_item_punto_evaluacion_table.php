<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValorItemPuntoEvaluacionTable extends Migration
{

    public function up()
    {
        Schema::create('valor_item_punto_evaluacion', function(Blueprint $table) {
            $table->increments('vie_id');
            $table->integer('vie_iev_id')->unsigned()->comment('identificador de item a evaluar');
            $table->string('vie_descripcion')->nullable()->comment('Indica el criterio de evaluación en palabras, por ejemplo: No existen escalones al ingresar');
            $table->integer('vie_orden')->unique()->comment('Orden de despliegue de las alternativas');
            $table->boolean('vie_publicado')->default(1)->comment('Indica si el criterio será visible en la aplicación o plataforma: 0:no publicada, 1:publicada');
            $table->boolean('vie_antigua')->default(0)->comment('Indica si son datos obtenidos de la base de datos anterior o no. (posible no precisión en la evaluación): 0:no, 1:sí');
            // Schema declaration
            // Constraints declaration
            $table->foreign('vie_iev_id')
                    ->references('iev_id')
                    ->on('item_evaluar');
        });
    }

    public function down()
    {
        Schema::drop('valor_item_punto_evaluacion');
    }
}
