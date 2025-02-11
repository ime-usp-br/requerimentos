<?php

namespace Database\Factories;

use App\Enums\Course;
use App\Enums\Department;
use App\Enums\DisciplineType;
use App\Enums\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequisitionsVersionFactory extends Factory
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
            'version' => $this->faker->numberBetween(1, 10),
            'requisition_id' => $this->faker->numberBetween(1, 20),
        ];
    }
}
