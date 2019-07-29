<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOpinionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('opinion_usuario_peval', 'votacion_usuario_peval');

        Schema::table('votacion_usuario_peval', function(Blueprint $table){
            $table->renameColumn('opu_id', 'vot_id');
            $table->renameColumn('opu_pev_id', 'vot_pev_id');
            $table->renameColumn('opu_usu_id', 'vot_usu_id');
            $table->renameColumn('opu_intencion_votacion', 'vot_intencion_votacion');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('votacion_usuario_peval', 'opinion_usuario_peval');

        Schema::table('opinion_usuario_peval', function(Blueprint $table){
            $table->renameColumn('vot_id', 'opu_id');
            $table->renameColumn('vot_pev_id', 'opu_pev_id');
            $table->renameColumn('vot_usu_id', 'opu_usu_id');
            $table->renameColumn('vot_intencion_votacion', 'opu_intencion_votacion');

        });
    }
}
