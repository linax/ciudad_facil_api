<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeImagenPuntoEvaluacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('imagen_punto_evaluacion', 'imagen_evaluacion');

        Schema::table('imagen_evaluacion', function(Blueprint $table){
            $table->dropForeign('imagen_punto_evaluacion_ima_pev_id_foreign');
            $table->integer('ima_eup_id')->unsigned()->comment('identificador de la evaluación a la que pertenece la imágen');
            $table->foreign('ima_eup_id')
                    ->references('eup_id')
                    ->on('evaluacion_usuario_punto_evaluacion');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('imagen_evaluacion', 'imagen_punto_evaluacion');

        Schema::table('imagen_punto_evaluacion', function(Blueprint $table){
            $table->dropForeign('evaluacion_usuario_punto_evaluacion_ima_eup_id_foreign');
            $table->integer('ima_pev_id')->unsigned()->comment('identificador del punto de evaluación al que pertenece la imágen');
            $table->foreign('ima_pev_id')
                    ->references('pev_id')
                    ->on('punto_evaluacion');
        });
    }
}
