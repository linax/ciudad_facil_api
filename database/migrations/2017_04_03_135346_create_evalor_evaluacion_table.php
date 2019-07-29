<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvalorEvaluacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('valor_evaluacion', function(Blueprint $table) {
            $table->integer('vae_vie_id')->unsigned()->comment('identificador del valor item punto evaluación');
            $table->integer('vae_eup_id')->unsigned()->comment('identificador del evaluacion usuario punto evaluación');

            $table->foreign('vae_vie_id')
                    ->references('vie_id')
                    ->on('valor_item_punto_evaluacion');

            $table->foreign('vae_eup_id')
                    ->references('eup_id')
                    ->on('evaluacion_usuario_punto_evaluacion');

            $table->unique(['vae_vie_id', 'vae_eup_id']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('valor_evaluacion');
    }
}
