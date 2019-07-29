<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagenEstacionamientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagen_estacionamiento', function(Blueprint $table) {
            $table->increments('ime_id');
            $table->integer('ime_est_id')->unsigned()->comment('Identificador de estacionamiento al que pertenece la imagen');
            $table->string('ime_imagen')->comment('Ruta al archivo de imágen');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ime_est_id')
                    ->references('est_id')
                    ->on('estacionamiento')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('imagen_estacionamiento');
    }
}
