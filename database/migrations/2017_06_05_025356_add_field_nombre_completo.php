<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldNombreCompleto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuario_social', function (Blueprint $table) {
            $table->string('uss_nombre_completo')->nullable()->comment('Nombre capturado de red social')->after('uss_proveedor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuario_social', function (Blueprint $table) {
            $table->dropColumn('uss_nombre_completo');
        });
    }
}
