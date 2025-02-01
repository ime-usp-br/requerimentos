<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('reviewer_decision', ['Sem decisão', 'Deferido', 'Indeferido']);
            $table->foreignId('requisition_id')->constrained()->cascadeOnDelete();
            $table->text('justification')->nullable();
            $table->unsignedInteger('reviewer_nusp')->nullable();
            $table->string('reviewer_name')->nullable();
            $table->unsignedBigInteger('latest_version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
