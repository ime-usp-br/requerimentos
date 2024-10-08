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
            $table->enum('type', [EventType::ACCEPTED, EventType::BACK_TO_STUDENT, EventType::REJECTED, EventType::RETURNED_BY_REVIEWER, EventType::SENT_TO_REVIEWERS, EventType::SENT_TO_SG, EventType::IN_REVALUATION, EventType::RESEND_BY_STUDENT]);
            $table->string('message')->nullable();
            $table->foreignId('requisition_id')->constrained()->cascadeOnDelete();
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
