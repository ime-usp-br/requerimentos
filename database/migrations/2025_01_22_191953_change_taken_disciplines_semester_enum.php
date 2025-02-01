<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;



class ChangeTakenDisciplinesSemesterEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taken_disciplines', function (Blueprint $table){
            DB::statement("ALTER TABLE `taken_disciplines` CHANGE `semester` `semester` ENUM('Primeiro', 'Segundo', 'Anual');");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taken_disciplines', function (Blueprint $table){
            DB::statement("ALTER TABLE `taken_disciplines` CHANGE `semester` `semester` ENUM('Primeiro', 'Segundo');");
        });
    }
}
