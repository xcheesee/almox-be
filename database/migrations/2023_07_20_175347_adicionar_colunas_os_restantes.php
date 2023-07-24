<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionarColunasOsRestantes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saidas', function (Blueprint $table) {
            //
            $table->text('especificacao')->nullable()->after('justificativa_os');
            $table->text('observacoes')->nullable()->after('especificacao');
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
            $table->dropColumn('especificacao');
            $table->dropColumn('observacoes');
        });
    }
}
