<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $durations = [15, 30, 45, 60];
        $names = ['Men\'s cut', 'Beard trim', 'Hair and beard', 'Kids cut', 'Senior cut', 'Buzz cut'];

        return [
            'name' => fake()->randomElement($names),
            'description' => fake()->optional(0.5)->sentence(),
            'duration_minutes' => fake()->randomElement($durations),
            'price' => fake()->randomFloat(2, 10, 75),
        ];
    }
}
