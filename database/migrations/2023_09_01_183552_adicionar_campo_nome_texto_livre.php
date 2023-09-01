<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionarCampoNomeTextoLivre extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saida_profissionais', function (Blueprint $table) {
            $table->unsignedBigInteger('profissional_id')->nullable()->change();
            $table->string('nome',100)->nullable()->after('profissional_id');
        });

        Schema::table('ordem_servico_profissionais', function (Blueprint $table) {
            $table->unsignedBigInteger('profissional_id')->nullable()->change();
            $table->string('nome',100)->nullable()->after('profissional_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('saida_profissionais', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('profissional_id')->nullable(false)->change();
            $table->dropColumn('nome');
        });
        Schema::table('ordem_servico_profissionais', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('profissional_id')->nullable(false)->change();
            $table->dropColumn('nome');
        });
    }
}
