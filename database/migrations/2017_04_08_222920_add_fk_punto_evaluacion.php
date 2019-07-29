<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkPuntoEvaluacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('punto_evaluacion', function (Blueprint $table) {
            $table->dropColumn('pev_tipo');
            $table->integer('pev_tpe_id')->after('pev_id')->unsigned()->comment('identificador del tipo de punto de evlauación');
            $table->foreign('pev_tpe_id')
                    ->references('tpe_id')
                    ->on('tipo_punto_evaluacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('punto_evaluacion', function (Blueprint $table) {
            $table->dropForeign('punto_evaluacion_pev_tpe_id_foreign');
            $table->tinyInteger('pev_tpe_id')->change();
            $table->renameColumn('pev_tipo', 'pev_tpe_id');
        });
    }
}
