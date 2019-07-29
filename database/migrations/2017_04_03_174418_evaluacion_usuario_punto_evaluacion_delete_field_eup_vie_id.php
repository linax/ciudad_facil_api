<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EvaluacionUsuarioPuntoEvaluacionDeleteFieldEupVieId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluacion_usuario_punto_evaluacion', function (Blueprint $table){
            $table->dropForeign(['eup_vie_id']);
            $table->dropColumn('eup_vie_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
