<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluacionUsuarioPuntoEvaluacionTable extends Migration
{

    public function up()
    {
        Schema::create('evaluacion_usuario_punto_evaluacion', function(Blueprint $table) {
            $table->increments('eup_id');
            $table->integer('eup_usu_id')->unsigned()->comment('Identificador de usuario que evalua');
            $table->integer('eup_pev_id')->unsigned()->comment('Identificador de punto de evaluación evlauado');
            $table->integer('eup_vie_id')->unsigned()->comment('Identificador de item de evaluacion');
            $table->string('eup_comentario')->comment('Comentario asociado a la evaluación');
            $table->timestamps();
            $table->softDeletes();
            // Schema declaration
            // Constraints declaration
            $table->foreign('eup_usu_id')
                    ->references('usu_id')
                    ->on('usuario');

            $table->foreign('eup_pev_id')
                    ->references('pev_id')
                    ->on('punto_evaluacion');

            $table->foreign('eup_vie_id')
                    ->references('vie_id')
                    ->on('valor_item_punto_evaluacion');
        });
    }

    public function down()
    {
        Schema::drop('evaluacion_usuario_punto_evaluacion');
    }
}
