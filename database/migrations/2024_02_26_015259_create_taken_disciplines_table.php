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
            // isso tem que se chamar requisition_id para a chave estrangeira funcionar 
            $table->foreignId('requisition_id')->constrained()->cascadeOnDelete();
            $table->string("name");
            $table->string("code");
            $table->unsignedInteger("year");
            $table->enum("semester", ["Primeiro", "Segundo"]);
            // verificar se a entrada do usuario se conforma a isso na rota
            $table->float("grade", 4, 2);
            $table->string("institution");
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
