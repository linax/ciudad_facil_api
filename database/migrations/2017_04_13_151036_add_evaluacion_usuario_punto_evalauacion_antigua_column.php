<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEvaluacionUsuarioPuntoEvalauacionAntiguaColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluacion_usuario_punto_evaluacion', function (Blueprint $table) {
            $table->boolean('eup_antigua')->after('eup_comentario')->nullable()->comment('indica si el dato proviene de la base de datos de la aplicación antigua');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evaluacion_usuario_punto_evaluacion', function (Blueprint $table) {
            //
        });
    }
}
