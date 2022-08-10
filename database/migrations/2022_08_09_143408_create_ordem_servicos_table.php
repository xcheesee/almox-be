<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdemServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordem_servicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained();
            $table->foreignId('origem_id')->constrained('locais');
            $table->foreignId('destino_id')->constrained('locais');
            $table->integer('local_servico_id');
            $table->string('almoxarife_nome',100);
            $table->string('almoxarife_email',100);
            $table->string('almoxarife_cargo',45)->nullable();
            $table->dateTime('data_servico');
            $table->text('especificacao')->nullable();
            $table->string('profissional',100)->nullable();
            $table->integer('horas_execucao')->nullable();
            $table->text('observacoes')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordem_servicos');
    }
}
