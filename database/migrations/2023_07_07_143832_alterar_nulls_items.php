<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterarNullsItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saida_items', function (Blueprint $table) {
            //
            $table->float('retorno',16,2)->nullable()->change();
            $table->float('usado',16,2)->nullable()->change();
            $table->float('enviado',16,2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('saida_items', function (Blueprint $table) {
            //
        });
    }
}
