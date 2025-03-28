<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\EventType;
use Illuminate\Support\Facades\DB;

class UpdateTypeOnEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE events MODIFY COLUMN `type` ENUM(" . "'" . EventType::ACCEPTED . "', " . 
                                                                        "'" . EventType::BACK_TO_STUDENT . "', " . 
                                                                        "'" . EventType::REJECTED . "', " . 
                                                                        "'" . EventType::RETURNED_BY_REVIEWER . "', " . 
                                                                        "'" . EventType::SENT_TO_REVIEWERS . "', " . 
                                                                        "'" . EventType::UPDATED_BY_STUDENT . "'," . 
                                                                        "'" . EventType::UPDATED_BY_SG . "'," . 
                                                                        "'" . EventType::SENT_TO_SG . "', " . 
                                                                        "'" . EventType::IN_REVALUATION . "', " . 
                                                                        "'" . EventType::RESENT_BY_STUDENT . "', " . 
                                                                        "'" . EventType::SENT_TO_DEPARTMENT . "', " . 
                                                                        "'" . EventType::REGISTERED . "')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
}
