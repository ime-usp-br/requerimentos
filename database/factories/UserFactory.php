<?php

namespace Database\Factories;

use App\Enums\RoleId;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'codpes' => $this->faker->numberBetween(10000000, 99999999),
            'email_verified_at' => now(),
            'current_role_id' => $this->faker->randomElement([RoleId::MAC_SECRETARY, RoleId::MAE_SECRETARY, RoleId::MAP_SECRETARY, RoleId::MAT_SECRETARY, RoleId::VRT_SECRETARY, RoleId::REVIEWER, RoleId::SG, RoleId::STUDENT]),
            // 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
