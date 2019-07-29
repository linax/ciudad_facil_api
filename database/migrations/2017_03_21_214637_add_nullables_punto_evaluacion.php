<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\PuntoEvaluacion;
use App\Estacionamiento;

class AddNullablesPuntoEvaluacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('punto_evaluacion', function(Blueprint $table){
            //$table->integer('pev_com_id')->nullable()->change();
            $table->integer('pev_votos_positivos')->nullable()->change();
            $table->integer('pev_votos_negativos')->nullable()->change();
            $table->float('pev_evaluacion_ponderada')->nullable()->change();
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
