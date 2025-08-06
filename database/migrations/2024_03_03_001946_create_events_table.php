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
            $table->enum('type', EventType::values());
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
