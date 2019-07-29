<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpinionUsuarioPevalTable extends Migration
{

    public function up()
    {
        Schema::create('opinion_usuario_peval', function(Blueprint $table) {
            $table->increments('opu_id');
            $table->integer('opu_pev_id')->unsigned()->comment('Punto de evaluación al que pertenece la evaluación');
            $table->integer('opu_usu_id')->unsigned()->comment('Usuario que hace la evaluación');
            $table->boolean('opu_intencion_votacion')->nullable()->comment('0: positiva, 1: negativa');
            $table->string('opu_comentario')->comment('Comentario en la evaluación');
            $table->timestamps();
            $table->softDeletes();
            // Schema declaration
            // Constraints declaration
            $table->foreign('opu_pev_id')
                    ->references('pev_id')
                    ->on('punto_evaluacion');

            $table->foreign('opu_usu_id')
                    ->references('usu_id')
                    ->on('usuario');
        });
    }

    public function down()
    {
        Schema::drop('opinion_usuario_peval');
    }
}
