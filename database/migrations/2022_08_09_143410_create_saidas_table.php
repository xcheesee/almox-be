<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained();
            $table->foreignId('ordem_servico_id')->constrained();
            $table->string('almoxarife_nome',100);
            $table->string('almoxarife_email',100);
            $table->string('almoxarife_cargo',45)->nullable();
            $table->integer('baixa_user_id');
            $table->dateTime('baixa_datahora');
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
        Schema::dropIfExists('saidas');
    }
}
