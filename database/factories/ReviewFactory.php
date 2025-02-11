<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'justification' => $this->faker->sentence(40),
            'requisition_id' => $this->faker->numberBetween(1, 50),
            'reviewer_nusp' => $this->faker->numberBetween(10000000, 99999999),
            'reviewer_name' => $this->faker->name(),
            'reviewer_decision' => $this->faker->randomElement(['Sem decisão', 'Deferido', 'Indeferido']),
            'latest_version' => 1
        ];
    }
}
