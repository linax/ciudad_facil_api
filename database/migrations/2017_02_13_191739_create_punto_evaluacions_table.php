<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePuntoEvaluacionsTable extends Migration
{

    public function up()
    {
        Schema::create('punto_evaluacion', function(Blueprint $table) {
            $table->increments('pev_id');
            $table->string('pev_titulo')->comment('nombre del punto de evaluación');
            $table->tinyinteger('pev_tipo')->comment('tipo de punto (definir la nomenclatura)');
            $table->integer('pev_usu_id')->unsigned()->comment('identificador de usuario que identifica el punto de evaluación');
            $table->integer('pev_com_id')->unsigned()->comment('identificador de comuna a la que pertenece el punto');
            $table->float('pev_latitud')->comment('ubicación según latitud del punto');
            $table->float('pev_longitud')->comment('ubicación según longitud del punto');
            $table->integer('pev_votos_positivos')->comment('número de votos positivos al punto de valuación');
            $table->integer('pev_votos_negativos')->comment('número de votos negativos sobre el punto de evaluación');
            $table->float('pev_evaluacion_ponderada')->comment('evaluación ponderada según criterio y tipo de punto');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('pev_usu_id')
                ->references('usu_id')
                ->on('usuario');
            $table->foreign('pev_com_id')
                ->references('com_id')
                ->on('comuna');
        });
    }

    public function down()
    {
        Schema::drop('punto_evaluacion');
    }
}
