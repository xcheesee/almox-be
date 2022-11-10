<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionarCampoLocalDepto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profissionais', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('local_id')->after('id')->nullable();
            $table->unsignedBigInteger('departamento_id')->after('id')->nullable();
            //Foreigns
            $table->foreign('local_id')->references('id')->on('locais');
            $table->foreign('departamento_id')->references('id')->on('departamentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profissionais', function (Blueprint $table) {
            //
            $table->dropForeign(['local_id']);
            $table->dropForeign(['departamento_id']);
        });
    }
}
