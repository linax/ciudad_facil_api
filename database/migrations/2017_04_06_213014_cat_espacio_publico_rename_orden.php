<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CatEspacioPublicoRenameOrden extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoria_espacio_publico', function(Blueprint $table) {
            $table->renameColumn('ces_orden_despliegue', 'ces_orden');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categoria_espacio_publico', function(Blueprint $table) {
            $table->renameColumn('ces_orden', 'ces_orden_despliegue');
        });
    }
}
