<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlteracoesLogicaOsVsSaida extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            //
            $table->dropColumn('numero_ordem_servico');
            $table->dropColumn('justificativa_os');
            $table->dropColumn('data_inicio_servico');
            $table->dropColumn('data_fim_servico');
            $table->dropColumn('flg_baixa');
            $table->dropColumn('status');
        });

        Schema::table('saidas', function(Blueprint $table){
            $table->unsignedBigInteger('ordem_servico_id')->nullable()->change();
            $table->text('justificativa_os')->nullable()->after('ordem_servico_id');
            $table->enum('status',['A iniciar','Iniciada','Finalizada'])->default('A iniciar')->after('justificativa_os');
            $table->boolean('flg_baixa')->default(false)->after('status');
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
            //
        });
    }
}
