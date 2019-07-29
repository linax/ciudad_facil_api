<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyUsuarioSocial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuario_social', function (Blueprint $table){
            $table->softDeletes();
            $table->integer('uss_usu_id')->unsigned()->change();
            $table->foreign('uss_usu_id')
                    ->references('usu_id')
                    ->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuario_social', function(Blueprint $table){
            $table->dropSoftDeletes();
            $table->integer('uss_usu_id')->change();
            $table->dropForeign(['uss_usu_id']);
        });
    }
}
