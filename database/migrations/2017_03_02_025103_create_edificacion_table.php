<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEdificacionTable extends Migration
{

    public function up()
    {
        Schema::create('edificacion', function(Blueprint $table) {
            $table->increments('edi_id');
            $table->integer('edi_sub_id')->unsigned()->comment('id de subcategoría de edificacion');
            $table->integer('edi_pev_id')->unsigned()->comment('id de punto de evaluación');
            $table->string('edi_direccion')->nullable()->comment('dirección de la edificación');
            $table->string('edi_email')->nullable()->comment('email asociado a la edificación');
            $table->string('edi_telefono')->nullable()->comment('teléfono asociado a edificación');
            $table->boolean('edi_publico')->default(0)->comment('indicador si la edificación es pública: default 0 (no)');
            // Schema declaration
            // Constraints declaration
            $table->foreign('edi_sub_id')
                    ->references('sub_id')
                    ->on('subcategoria_edificacion');

            $table->foreign('edi_pev_id')
                    ->references('pev_id')
                    ->on('punto_evaluacion');
        });
    }

    public function down()
    {
        Schema::drop('edificacion');
    }
}
