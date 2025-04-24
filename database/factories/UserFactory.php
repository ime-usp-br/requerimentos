<?php

namespace Database\Factories;

use App\Enums\RoleId;
use App\Models\User;
use Spatie\Permission\Models\Role;
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
            'codpes' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'email_verified_at' => now(),
            'current_role_id' => $this->faker->randomElement([RoleId::STUDENT, RoleId::SG, RoleId::SECRETARY, RoleId::REVIEWER]),
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

    /**
     * Configure the factory to populate the model_has_roles table.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Find the role matching the user's current_role_id
            $role = Role::find($user->current_role_id);

            if ($role && $role->role_id != RoleId::STUDENT) {
                // Assign the role to the user in the model_has_roles table
                $user->assignRole($role->name);
            }
        });
    }
}
