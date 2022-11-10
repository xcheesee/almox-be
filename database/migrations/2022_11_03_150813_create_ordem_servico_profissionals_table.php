<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdemServicoProfissionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordem_servico_profissionais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_servico_id')->constrained();
            $table->foreignId('profissional_id')->constrained('profissionais');
            $table->date('data_inicio');
            $table->integer('horas_empregadas');
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
        Schema::dropIfExists('ordem_servico_profissionals');
    }
}
