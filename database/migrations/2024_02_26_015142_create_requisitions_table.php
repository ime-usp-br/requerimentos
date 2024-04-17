<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('department', ['MAC', 'MAE', 'MAT', 'MAP', 'Disciplina de fora do IME']);
            
            $table->unsignedInteger('nusp');
            $table->string('student_name');
            $table->string('email');
            
            $table->enum('course', ['Bacharelado em Ciência da Computação', 'Bacharelado em Estatística', 'Bacharelado em Matemática', 'Bacharelado em Matemática Aplicada', 'Bacharelado em Matemática Aplicada e Computacional', 'Licenciatura em Matemática']);
            $table->string('requested_disc');
            $table->enum('requested_disc_type', ['Extracurricular', 'Obrigatória', 'Optativa Eletiva', 'Optativa Livre']);
            $table->string('situation');
            $table->string('internal_status');
            $table->string('requested_disc_code');
            $table->text('observations')->nullable();

            // arquivos
            $table->string('taken_discs_record');
            $table->string('current_course_record');
            $table->string('taken_discs_syllabus');
            $table->string('requested_disc_syllabus');

            // resultado
            $table->enum('result', ['Sem resultado', 'Inconsistência nas informações', 'Deferido', 'Indeferido']);
            $table->text('result_text')->nullable();
            
            
            // parecerista
            // $table->enum('reviewer_decision', ['Sem decisão', 'Deferido', 'Indeferido']);
            // $table->text('appraisal')->nullable();
            // $table->unsignedInteger('reviewer_nusp')->nullable();
            // $table->string('reviewer_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisitions');
    }
}
