<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DomainFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'name'           => $this->faker->domainName(),
            'check_interval' => $this->faker->randomElement([1, 5, 10, 15, 30, 60]),
            'timeout'        => $this->faker->randomElement([5, 10, 15, 30]),
            'method'         => $this->faker->randomElement(['GET', 'HEAD']),
            'is_active'      => $this->faker->boolean(90),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
