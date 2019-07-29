<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVeredaCruceTable extends Migration
{

    public function up()
    {
        Schema::create('vereda_cruce', function(Blueprint $table) {
            $table->increments('vcr_id');
            $table->integer('vcr_pev_id')->unsigned()->comment('Identificador de punto de evaluación al que pertenece la vereda cruce');
            $table->tinyinteger('vcr_tipo')->comment('1:vereda, 2:cruce');
            $table->string('vcr_comentario_senadis')->nullable()->comment('Indica comentarios dejados por SENADIS cuando se realiza alguna mejora o modificación de la vereda o el cruce');
            $table->string('vcr_descripcion')->nullable()->comment('Descripción entregada por el usuario de la vereda o cruce.');
            $table->boolean('vcr_mejora_senadis')->default(0)->comment('Indica si SENADIS ha notificado de mejoras o no, de esta vereda o cruce.');
            $table->timestamps();
            $table->softDeletes();
            // Schema declaration
            // Constraints declaration
            $table->foreign('vcr_pev_id')
                    ->references('pev_id')
                    ->on('punto_evaluacion');
        });
    }

    public function down()
    {
        Schema::drop('vereda_cruce');
    }
}
