<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\EventType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('type', EventType::values())->change();
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
