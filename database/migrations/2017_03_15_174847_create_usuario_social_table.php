<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuarioSocialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_social', function (Blueprint $table) {
            $table->integer('uss_usu_id')->comment('identificador del usuario al que pertenece el dato');
            $table->string('uss_proveedor_usu_id')->comment('identificador de usuario de la red social');
            $table->string('uss_proveedor')->comment('identificador del proveedor/red social');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario_social');
    }
}
