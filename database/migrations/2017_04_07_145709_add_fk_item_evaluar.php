<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkItemEvaluar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_evaluar', function (Blueprint $table){
            $table->integer('iev_tpe_id')->after('iev_id')->unsigned()->comment('identificador del tipo de punto de evaluación');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return voidphp
     */
    public function down()
    {
        Schema::table('item_evaluar', function (Blueprint $table){
            $table->dropColumn('iev_tpe_id');
        });
    }
}
