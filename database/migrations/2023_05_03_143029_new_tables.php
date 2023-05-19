<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferencia_de_materiais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('base_origem_id');
            $table->unsignedBigInteger('base_destino_id');
            $table->dateTime('data_transferencia');
            $table->enum('status', ['enviado', 'recebido', 'recusado'])->default('enviado');
            $table->unsignedBigInteger('user_id');
            $table->text('observacao')->nullable();
            $table->enum('observacao_motivo', ['nao_enviado', 'itens_faltando', 'extravio', 'furto', 'avaria'])->nullable();
            $table->unsignedBigInteger('observacao_user_id')->nullable();
            $table->timestamps();

            
            $table->foreign('base_origem_id')->references('id')->on('locais');
            $table->foreign('base_destino_id')->references('id')->on('locais');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('observacao_user_id')->references('id')->on('users');
        });

        Schema::create('transferencia_itens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transferencia_materiais_id');
            $table->bigInteger('item_id');
            $table->bigInteger('quantidade');
            $table->timestamps();

            $table->foreign('transferencia_materiais_id')->references('id')->on('transferencia_de_materiais');
        });

        Schema::create('ocorrencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('local_id');
            $table->dateTime('data_ocorrencia');
            $table->enum('tipo_ocorrencia', ['avaria', 'furto', 'extravio']);
            $table->string('boletim_ocorrencia');
            $table->text('justificativa');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('local_id')->references('id')->on('locais');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('ocorrencia_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ocorrencia_id');
            $table->bigInteger('item_id');
            $table->bigInteger('quantidade');
            $table->timestamps();

            $table->foreign('ocorrencia_id')->references('id')->on('ocorrencias');
        });

        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->dropColumn('almoxarife_nome');
            $table->dropColumn('almoxarife_email');
            $table->dropColumn('profissional');
            $table->dropColumn('horas_execucao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transferencia_de_materiais', function (Blueprint $table) {
            $table->dropConstrainedForeignId('base_origem_id');
            $table->dropConstrainedForeignId('base_destino_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('observacao_user_id');
        });

        //Schema::table('transferencia_itens', function (Blueprint $table) {
        //    $table->dropColumn('transferencia_materiais_id');
        //});

        Schema::table('ocorrencias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('local_id');
            $table->dropConstrainedForeignId('user_id');
        });

        //Schema::table('ocorrencia_item', function (Blueprint $table) {
        //    $table->dropConstrainedForeignId('ocorrencia_id');
        //});

        Schema::dropIfExists('transferencia_de_materiais');
        Schema::dropIfExists('transferencia_itens');
        Schema::dropIfExists('ocorrencias');
        Schema::dropIfExists('ocorrencia_item');

        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->string('almoxarife_nome');
            $table->string('almoxarife_email');
            $table->string('profissional');
            $table->string('horas_execucao');
        });
    }
}
