<?php

namespace Roghumi\Press\Crud\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Roghumi\Press\Crud\Resources\Domain\Domain;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class DomainFactory extends Factory
{
    protected $model = Domain::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->domainName(),
            'author_id' => 1,
        ];
    }
}
