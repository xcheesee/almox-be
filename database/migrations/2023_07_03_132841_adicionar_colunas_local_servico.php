<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionarColunasLocalServico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saidas', function (Blueprint $table) {
            // $table->foreignId('origem_id')->constrained('locais');
            // $table->foreignId('local_servico_id')->constrained('locais');

            $table->unsignedBigInteger('origem_id')->nullable()->after('departamento_id');
            $table->unsignedBigInteger('local_servico_id')->nullable()->after('origem_id');

            $table->foreign('origem_id')->references('id')->on('locais');
            $table->foreign('local_servico_id')->references('id')->on('locais');
        });

        Schema::create('saida_profissionais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saida_id')->constrained();
            $table->foreignId('profissional_id')->constrained('profissionais');
            $table->date('data_inicio')->nullable();
            $table->integer('horas_empregadas')->nullable();
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
        Schema::table('saidas', function (Blueprint $table) {
            //
            $table->dropForeign('saidas_origem_id_foreign');
            $table->dropForeign('saidas_local_servico_id_foreign');
        });

        Schema::dropIfExists('saida_profissionais');
    }
}
