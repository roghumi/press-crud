<?php

namespace Roghumi\Press\Crud\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Roghumi\Press\Crud\Resources\Role\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->jobTitle(),
        ];
    }
}
