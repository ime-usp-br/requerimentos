<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews_versions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('reviewer_decision', ['Sem decisão', 'Deferido', 'Indeferido']);
            $table->foreignId('requisition_id')->constrained()->cascadeOnDelete();
            $table->text('justification')->nullable();
            $table->unsignedInteger('reviewer_nusp')->nullable();
            $table->string('reviewer_name')->nullable();

            // Informações para controle de versão
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews_versions');
    }
}
