<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEspacioPublicoTable extends Migration
{

    public function up()
    {
        Schema::create('espacio_publico', function(Blueprint $table) {
            $table->increments('esp_id');
            $table->integer('esp_pev_id')->unsigned()->comment('id de punto de evaluación');
            $table->integer('esp_ces_id')->unsigned()->comment('id de categoría de espacio público');
            $table->string('esp_direccion')->nullable()->comment('dirección del espacio público');
            $table->string('esp_email')->nullable()->comment('email asociado al espacio público');
            $table->string('esp_telefono')->nullable()->comment('teléfono asociado al espacio público');
            // Schema declaration
            // Constraints declaration
            $table->foreign('esp_ces_id')
                    ->references('ces_id')
                    ->on('categoria_espacio_publico');

            $table->foreign('esp_pev_id')
                    ->references('pev_id')
                    ->on('punto_evaluacion');
        });
    }

    public function down()
    {
        Schema::drop('espacio_publico');
    }
}
