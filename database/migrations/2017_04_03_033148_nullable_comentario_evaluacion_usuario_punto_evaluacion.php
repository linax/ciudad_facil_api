<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NullableComentarioEvaluacionUsuarioPuntoEvaluacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluacion_usuario_punto_evaluacion', function (Blueprint $table){
            $table->string('eup_comentario')->nullable()->change();
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
            $table->string('eup_comentario')->change();
        });
    }
}
