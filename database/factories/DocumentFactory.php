<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'requisition_id' => $this->faker->unique->numberBetween(1, 50),
            'type' => $this->faker->randomElement([DocumentType::TAKEN_DISCS_RECORD, DocumentType::CURRENT_COURSE_RECORD, DocumentType::TAKEN_DISCS_SYLLABUS, DocumentType::REQUESTED_DISC_SYLLABUS]),
            'path' => Str::random(20),
        ];
    }
}
