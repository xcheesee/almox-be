<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FKItemTransferencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transferencia_itens', function (Blueprint $table) {
            $table->renameColumn('entrada_id', 'transferencia_materiais_id');
        });

        Schema::table('transferencia_itens', function (Blueprint $table) {
            $table->unsignedBigInteger('transferencia_materiais_id')->change();
            
            $table->foreign('transferencia_materiais_id')->references('id')->on('transferencia_de_materiais');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transferencia_itens', function (Blueprint $table) {
            $table->renameColumn('transferencia_materiais_id', 'entrada_id');
        });
    }
}
