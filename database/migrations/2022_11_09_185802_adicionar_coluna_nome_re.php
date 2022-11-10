<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdicionarColunaNomeRe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('responsaveis_emails', function (Blueprint $table) {
            //
            $table->string("nome",60)->after("departamento_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('responsaveis_emails', function (Blueprint $table) {
            //
            $table->dropColumn("nome");
        });
    }
}
