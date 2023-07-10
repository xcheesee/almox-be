<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionarColunaSolicitado extends Migration
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
            $table->float('enviado',16,2)->nullable()->change();
            $table->float('quantidade',16,2)->nullable()->after("item_id");
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
            $table->dropColumn('quantidade');
        });
    }
}
