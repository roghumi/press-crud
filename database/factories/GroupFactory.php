<?php

namespace Roghumi\Press\Crud\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Roghumi\Press\Crud\Resources\Group\Group;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'BloodType '.fake()->bloodGroup(),
            'authorId' => 1,
        ];
    }
}
