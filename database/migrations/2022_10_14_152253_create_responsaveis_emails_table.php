<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsaveisEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responsaveis_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained();
            $table->string('email');
            $table->boolean('ativo')->default(1);
            $table->timestamps();
        });

        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->enum('status',['A iniciar','Iniciada','Finalizada'])->default('A iniciar')->after('local_servico_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('responsaveis_emails');
    }
}
