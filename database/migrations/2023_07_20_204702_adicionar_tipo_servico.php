<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionarTipoServico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_servicos', function(Blueprint $table){
            $table->id();
            $table->foreignId('departamento_id')->nullable()->constrained();
            $table->string('servico',25);
            $table->timestamps();
        });

        Schema::table('saidas', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('tipo_servico_id')->nullable()->after('departamento_id');
            $table->foreign('tipo_servico_id')->references('id')->on('tipo_servicos');
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
            $table->dropConstrainedForeignId('tipo_servico_id');
        });

        Schema::dropIfExists('tipo_servicos');
    }
}
