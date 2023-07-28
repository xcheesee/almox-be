<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterarNulls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saidas', function (Blueprint $table) {
            //
            //$table->unsignedBigInteger('baixa_user_id')->nullable()->change();
            $table->dateTime('baixa_datahora')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('saidas', function (Blueprint $table) {
            //
        });
    }
}
