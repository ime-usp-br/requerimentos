<?php

use App\Enums\EventType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('type', [
                EventType::ACCEPTED,
                EventType::BACK_TO_STUDENT,
                EventType::REJECTED,
                EventType::RETURNED_BY_REVIEWER,
                EventType::SENT_TO_REVIEWERS,
                EventType::UPDATED_BY_STUDENT,
                EventType::UPDATED_BY_SG,
                EventType::SENT_TO_SG,
                EventType::IN_REVALUATION,
                EventType::RESENT_BY_STUDENT,
                EventType::SENT_TO_DEPARTMENT,
                EventType::REGISTERED,
                EventType::AUTOMATIC_DEFERRAL,
                EventType::CANCELLED
            ]);
            $table->string('message')->nullable();
            $table->foreignId('requisition_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('author_name');
            $table->unsignedInteger('author_nusp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
