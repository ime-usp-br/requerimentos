<?php

namespace Database\Factories;

use App\Enums\RoleId;
use App\Enums\DepartmentId;
use App\Models\User;
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
        $roleId = $this->faker->randomElement([
            RoleId::STUDENT,
            RoleId::SG,
            RoleId::SECRETARY,
            RoleId::REVIEWER
        ]);

        $departmentIds = [
            DepartmentId::MAC,
            DepartmentId::MAE,
            DepartmentId::MAP,
            DepartmentId::MAT,
            DepartmentId::VRT,
        ];

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'codpes' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'email_verified_at' => now(),
            'current_role_id' => $roleId,
            'current_department_id' => function (array $attributes) use ($departmentIds) {
                $role = $attributes['current_role_id'] ?? null;
                if (in_array($role, [RoleId::SECRETARY, RoleId::REVIEWER])) {
                    return $this->faker->randomElement($departmentIds);
                }
                return null;
            },
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
            $user->assignRole($user->current_role_id, $user->current_department_id);
        });
    }
}
