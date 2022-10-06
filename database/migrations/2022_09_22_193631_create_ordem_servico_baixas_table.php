<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdemServicoBaixasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saida_items', function (Blueprint $table) {
            $table->dropColumn('quantidade');
            $table->float('retorno',16,2)->after("item_id");
            $table->float('usado',16,2)->after("item_id");
            $table->float('enviado',16,2)->after("item_id");
        });

        Schema::table('saidas', function (Blueprint $table) {
            $table->dropColumn('almoxarife_nome');
            $table->dropColumn('almoxarife_email');
            $table->dropColumn('almoxarife_cargo');
            //$table->integer('baixa_user_id')->after('baixa_datahora')->change();
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
            $table->dropColumn('enviado');
            $table->dropColumn('usado');
            $table->dropColumn('retorno');
            $table->float('quantidade',16,2)->after("item_id");
        });
    }
}
