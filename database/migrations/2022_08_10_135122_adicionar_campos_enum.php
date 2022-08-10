<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionarCamposEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locais', function (Blueprint $table) {
            $table->enum('tipo', ['base', 'parque', 'autarquia', 'secretaria', 'subprefeitura'])->after('nome');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->enum('tipo', ['pintura', 'hidraulica', 'carpintaria', 'alvenaria'])->after('nome');
        });
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->dropColumn('local_servico_id');
            $table->dropColumn('user_id');
        });
        Schema::table('saidas', function (Blueprint $table) {
            $table->dropColumn('baixa_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locais', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
}
