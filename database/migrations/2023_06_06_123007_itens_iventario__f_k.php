<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ItensIventarioFK extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transferencia_itens', function(Blueprint $table){
            $table->dropColumn('item_id');
        });

        Schema::table('ocorrencia_item', function(Blueprint $table){
            $table->dropColumn('item_id');
        });

        
        Schema::table('transferencia_itens', function(Blueprint $table){
            $table->unsignedBigInteger('item_id')->after('transferencia_materiais_id');
            
            $table->foreign('item_id')->references('item_id')->on('inventarios');
        });

        Schema::table('ocorrencia_item', function(Blueprint $table){
            $table->unsignedBigInteger('item_id')->after('ocorrencia_id');
            
            $table->foreign('item_id')->references('item_id')->on('inventarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
