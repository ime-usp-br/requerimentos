<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTakenDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taken_disciplines', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('requisition_id')->constrained()->cascadeOnDelete();
            $table->string("name");
            $table->string("code");
            $table->unsignedInteger("year");
            $table->enum("semester", ["Primeiro", "Segundo", "Anual"]);
            $table->string("grade");
            $table->string("institution");
            $table->unsignedInteger('version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taken_disciplines');
    }
}
