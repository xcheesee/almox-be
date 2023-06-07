<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OcorrenciaEnumNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ocorrencias', function(Blueprint $table){
            $table->dropColumn('tipo_ocorrencia');
        });

        Schema::table('ocorrencias', function(Blueprint $table){
            $table->enum('tipo_ocorrencia', ['avaria', 'furto', 'extravio'])->nullable()->after('data_ocorrencia');
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
