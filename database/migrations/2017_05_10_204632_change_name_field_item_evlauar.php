<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameFieldItemEvlauar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_evaluar', function(Blueprint $table) {
            $table->integer('iev_tipo')->comment('Tipo de punto de evaluación (separado para el caso de veredas y cruces) 1: estacionamiento, 2: edificación, 3: espacio público, 4: vereda, 5: cruce')->change();
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
