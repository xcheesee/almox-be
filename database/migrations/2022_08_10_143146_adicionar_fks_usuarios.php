<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionarFksUsuarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->unsignedBigInteger('local_servico_id')->after('destino_id');
            $table->unsignedBigInteger('user_id')->after('observacoes');
            //Foreigns
            $table->foreign('local_servico_id')->references('id')->on('locais');
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('saidas', function (Blueprint $table) {
            $table->unsignedBigInteger('baixa_user_id');
            //Foreigns
            $table->foreign('baixa_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('local_servico_id');
            $table->dropConstrainedForeignId('user_id');
        });
        Schema::table('saidas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('baixa_user_id');
        });
    }
}
