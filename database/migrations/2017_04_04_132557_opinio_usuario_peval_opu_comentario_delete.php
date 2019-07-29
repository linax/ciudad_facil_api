<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OpinioUsuarioPevalOpuComentarioDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opinion_usuario_peval', function (Blueprint $table) {
            $table->dropColumn('opu_comentario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opinion_usuario_peval', function (Blueprint $table) {
            //
        });
    }
}
