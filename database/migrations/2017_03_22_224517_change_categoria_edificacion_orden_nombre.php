<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCategoriaEdificacionOrdenNombre extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoria_edificacion', function(Blueprint $table){
            $table->renameColumn('cat_orden_despliegue', 'cat_orden');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categoria_edificacion', function(Blueprint $table){
            $table->renameColumn('cat_orden', 'cat_orden_despliegue');
        });
    }
}

