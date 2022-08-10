<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->nullable()->constrained();
            $table->string('nome',100);
            //$table->enum('tipo', []);
            $table->string('cep',9)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero',5)->nullable();
            $table->string('bairro',45)->nullable();
            $table->string('cidade',60)->nullable();
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
        Schema::dropIfExists('locais');
    }
}
