<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TakenDisciplinesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'requisition_id' => $this->faker->unique()->numberBetween(1, 99999999),
            'code' => $this->faker->word(),
            'year' => $this->faker->year(),
            'semester' => $this->faker->randomElement(['Primeiro', 'Segundo']),
            'grade' => $this->faker->randomFloat(2, 0, 10),
            'institution' => $this->faker->sentence(3),
            'latest_version' => $this->faker->numberBetween(1, 10),
        ];
    }
}
