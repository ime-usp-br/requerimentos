<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\Course;
use App\Enums\DisciplineType;
use App\Enums\DepartmentName;
use App\Enums\ResultType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->enum('department', DepartmentName::values())->change();
            $table->enum('course', Course::values())->change();
            $table->enum('requested_disc_type', DisciplineType::values())->change();
            $table->enum('result', ResultType::values())->change();
        });
    }

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::table('requisitions', function (Blueprint $table) {
        $table->enum('department', ['MAC', 'MAE', 'MAT', 'MAP', 'Disciplina de fora do IME'])->change();
        $table->enum('course', ['Bacharelado em Ciência da Computação', 'Bacharelado em Estatística', 'Bacharelado em Matemática', 'Bacharelado em Matemática Aplicada', 'Bacharelado em Matemática Aplicada e Computacional', 'Licenciatura em Matemática'])->change();
        $table->enum('requested_disc_type', ['Extracurricular', 'Obrigatória', 'Optativa Eletiva', 'Optativa Livre'])->change();
        $table->enum('result', ['Sem resultado', 'Inconsistência nas informações', 'Deferido', 'Indeferido'])->change();
    });
}
};