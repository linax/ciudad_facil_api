<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpinionComentario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opinion_comentario');
    }
}
