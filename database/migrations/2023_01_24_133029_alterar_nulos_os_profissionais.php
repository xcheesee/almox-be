<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterarNulosOsProfissionais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordem_servico_profissionais', function (Blueprint $table) {
            //
            $table->date('data_inicio')->nullable()->change();
            $table->integer('horas_empregadas')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordem_servico_profissionais', function (Blueprint $table) {
            //
            $table->date('data_inicio')->change();
            $table->integer('horas_empregadas')->change();
        });
    }
}
