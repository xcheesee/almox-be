<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionarDeletesLogicos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->boolean('ativo')->default(1)->nullable()->after('arquivo_nota_fiscal');
        });

        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->boolean('ativo')->default(1)->nullable()->after('observacoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });
    }
}
