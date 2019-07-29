<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeComentarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('opinion_comentario');

        Schema::create('comentario_usuario_peval', function (Blueprint $table){
            $table->increments('com_id');
            $table->integer('com_pev_id')->unsigned()->comment('identificador del punto de evaluación del comentaro');
            $table->integer('com_usu_id')->unsigned()->comment('identificdor del usuario que comenta');
            $table->string('com_comentario');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('com_pev_id')
                            ->references('pev_id')
                            ->on('punto_evaluacion');

            $table->foreign('com_usu_id')
                            ->references('usu_id')
                            ->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comentario_usuario_pval');

        Schema::create('opinion_comentario', function (Blueprint $table) {
            $table->increments('opc_id');
            $table->integer('opc_opu_id')->unsigned()->comment('identificador de la opinión sobre la cual se hace el comentario');
            $table->string('opc_comentario')->comment('comentario sobre la opinión');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('opc_opu_id')
                ->references('opu_id')
                ->on('opinion_usuario_peval');
        });
    }
}
