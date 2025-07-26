<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\Course;
use App\Enums\DisciplineType;
use App\Enums\DepartmentName;
use App\Enums\ResultType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->enum('department', DepartmentName::values())->change();
            $table->enum('course', Course::values())->change();
            $table->enum('requested_disc_type', DisciplineType::values())->change();
            $table->enum('result', ResultType::values())->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
        });
    }
};