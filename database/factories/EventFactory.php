<?php

namespace Database\Factories;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'message' => $this->faker->sentence(3),
            'version' => $this->faker->numberBetween(1, 10),
            'requisition_id' => $this->faker->numberBetween(1, 20),
            'author_name' => $this->faker->name(),
            'type' => $this->faker->randomElement([EventType::ACCEPTED, EventType::BACK_TO_STUDENT, EventType::REJECTED, EventType::RETURNED_BY_REVIEWER, EventType::SENT_TO_REVIEWERS, EventType::SENT_TO_SG, EventType::IN_REVALUATION, EventType::RESENT_BY_STUDENT, EventType::SENT_TO_DEPARTMENT]),
            'author_nusp' => $this->faker->numberBetween(10000000, 99999999),
        ];
    }
}
