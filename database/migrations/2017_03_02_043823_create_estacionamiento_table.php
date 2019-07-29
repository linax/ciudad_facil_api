<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstacionamientoTable extends Migration
{

    public function up()
    {
        Schema::create('estacionamiento', function(Blueprint $table) {
            $table->increments('est_id');
            $table->integer('est_pev_id')->unsigned()->comment('Identificador de punto de evaluación al que pertenece el estacionamiento');
            $table->integer('est_cantidad_total')->comment('Cantidad total de estacionamientos.');
            $table->integer('est_cantidad_reservados')->comment('Cantidad de estacionamientos reservados');
            $table->boolean('est_cumple_ordenanza')->default(0)->comment('Cumplimiento de ordenanza municipal: 0:no cumple, 1:cumple');
            $table->string('est_comentario')->nullable()->comment('Comentados dejados por los usuarios');
            // Schema declaration
            // Constraints declaration
            $table->foreign('est_pev_id')
                    ->references('pev_id')
                    ->on('punto_evaluacion')
                    ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('estacionamiento');
    }
}
