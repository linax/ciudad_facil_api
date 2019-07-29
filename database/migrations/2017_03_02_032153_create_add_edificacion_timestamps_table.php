<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddEdificacionTimestampsTable extends Migration
{

    public function up()
    {
        Schema::table('edificacion', function(Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('edificacion');
    }
}
