<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionsVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // essa tabela contém versões anteriores dos requerimentos, armazenadas
        // quando qualquer campo do requerimento/disciplinas é modificado. A tabela 
        // requisitions contém sempre a versão mais recente. 
        Schema::create('requisitions_versions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('department', ['MAC', 'MAE', 'MAT', 'MAP', 'Disciplina de fora do IME']);
            
            $table->unsignedInteger('student_nusp');

            // chave primária (id do requerimento na tabela requisitions + 
            // número dessa versão)
            $table->foreignId('requisition_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('version');

            $table->string('student_name');
            $table->string('email');
            
            $table->enum('course', ['Bacharelado em Ciência da Computação', 'Bacharelado em Estatística', 'Bacharelado em Matemática', 'Bacharelado em Matemática Aplicada', 'Bacharelado em Matemática Aplicada e Computacional', 'Licenciatura em Matemática']);
            $table->string('requested_disc');
            $table->enum('requested_disc_type', ['Extracurricular', 'Obrigatória', 'Optativa Eletiva', 'Optativa Livre']);
            // $table->string('situation');
            // $table->string('internal_status');
            $table->string('requested_disc_code');
            // $table->boolean('validated');
            $table->text('observations')->nullable();

            // resultado
            $table->enum('result', ['Sem resultado', 'Inconsistência nas informações', 'Deferido', 'Indeferido']);
            $table->text('result_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisitions_versions');
    }
}
