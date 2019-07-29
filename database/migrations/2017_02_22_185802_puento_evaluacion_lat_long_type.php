<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PuentoEvaluacionLatLongType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('punto_evaluacion', function(Blueprint $table){
            $table->decimal('pev_latitud', 10, 8)->change(); // -90/90
            $table->decimal('pev_longitud', 11, 8)->change();// -180/180
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
