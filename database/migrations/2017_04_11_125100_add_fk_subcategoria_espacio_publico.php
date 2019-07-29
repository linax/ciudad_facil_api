<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkSubcategoriaEspacioPublico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcategoria_espacio_publico', function (Blueprint $table) {
            $table->integer('ces_cep_id')->after('ces_id')->unsigned()->nullable()->comment('identificador de la categoría mayor a la que pertenece');
            $table->foreign('ces_cep_id')
                            ->references('cep_id')
                            ->on('categoria_espacio_publico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcategoria_espacio_publico', function (Blueprint $table) {
            $table->dropForeign('subcategoria_espacio_publico_ces_cep_id_fk');
            $table->dropColumn('ces_cep_id');
        });
    }
}
