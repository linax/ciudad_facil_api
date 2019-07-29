<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexItemEvaluar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('item_evaluar', function (Blueprint $table){
            $table->foreign('iev_tpe_id')
                ->references('tpe_id')
                ->on('tipo_punto_evaluacion');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_evaluar', function (Blueprint $table){
            $table->dropForeign('item_evaluar_iev_tpp_id_foreign');
        });
    }
}
