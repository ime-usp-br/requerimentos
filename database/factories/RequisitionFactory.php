<?php

namespace Database\Factories;

use App\Enums\Course;
use App\Enums\Department;
use App\Enums\DisciplineType;
use App\Enums\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequisitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'department' => $this->faker->randomElement([Department::EXTERNAL, Department::MAC, Department::MAE, Department::MAT, Department::MAP]), 
            'requested_disc' => $this->faker->sentence(3),
            'requested_disc_type' => $this->faker->randomElement([DisciplineType::EXTRACURRICULAR, DisciplineType::MANDATORY, DisciplineType::OPTIONAL_ELECTIVE, DisciplineType::OPTIONAL_FREE]),
            'requested_disc_code' => $this->faker->word(),
            'student_name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'student_nusp' => $this->faker->numberBetween(10000000, 99999999),
            'course' => $this->faker->randomElement([Course::BCC, Course::MAT_APPLIED, Course::MAT_COMP_APPLIED, Course::MAT_LIC, Course::MAT_PURE, Course::STATISTICS]),
            'result' => 'Sem resultado',
            'observations' => $this->faker->sentence(30),
            'result_text' => $this->faker->sentence(30),
            'situation' => $this->faker->randomElement([EventType::ACCEPTED, EventType::BACK_TO_STUDENT, EventType::REJECTED, EventType::RETURNED_BY_REVIEWER, EventType::SENT_TO_REVIEWERS, EventType::SENT_TO_SG, EventType::IN_REVALUATION, EventType::RESEND_BY_STUDENT, EventType::SENT_TO_DEPARTMENT]),
            'internal_status' => $this->faker->randomElement([EventType::ACCEPTED, EventType::BACK_TO_STUDENT, EventType::REJECTED, EventType::RETURNED_BY_REVIEWER, EventType::SENT_TO_REVIEWERS, EventType::SENT_TO_SG, EventType::IN_REVALUATION, EventType::RESEND_BY_STUDENT, EventType::SENT_TO_DEPARTMENT]),
            'validated' => $this->faker->randomElement([True, False]),
            'latest_version' => $this->faker->numberBetween(1, 10),
        ];
    }
}
