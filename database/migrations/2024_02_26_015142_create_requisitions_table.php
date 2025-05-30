<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\Course;
use App\Enums\DisciplineType;
use App\Enums\DepartmentName;
use App\Enums\ResultType;

class CreateRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisitions', function (Blueprint $table) {

            $table->id();
            $table->timestamps();
            $table->enum('department', [
                DepartmentName::MAC,
                DepartmentName::MAE,
                DepartmentName::MAP,
                DepartmentName::MAT,
                DepartmentName::EXTERNAL
            ]);
            $table->unsignedInteger('student_nusp');
            $table->unsignedInteger('latest_version');
            $table->string('student_name');
            $table->string('email');
            $table->enum('course', [
                Course::BCC,
                Course::STATISTICS,
                Course::MAT_LIC,
                Course::MAT_PURE,
                Course::MAT_COMP_APPLIED,
                Course::MAT_APPLIED
            ]);
            $table->string('requested_disc');
            $table->enum('requested_disc_type', [
                DisciplineType::MANDATORY,
                DisciplineType::EXTRACURRICULAR,
                DisciplineType::OPTIONAL_FREE,
                DisciplineType::OPTIONAL_ELECTIVE
            ]);
            $table->string('situation');
            $table->string('internal_status');
            $table->string('requested_disc_code');
            $table->text('observations')->nullable();
            $table->enum('result', [
                ResultType::PENDING,
                ResultType::ACCEPTED,
                ResultType::REJECTED,
                ResultType::INCONSISTENT,
                ResultType::CANCELLED
            ]);
            $table->text('result_text')->nullable();
            $table->boolean('editable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisitions');
    }
}
