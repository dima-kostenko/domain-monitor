<?php

namespace Database\Factories;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;

class DomainCheckFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['online', 'offline']);

        return [
            'domain_id'     => Domain::factory(),
            'status'        => $status,
            'response_code' => $status === 'online'
                ? $this->faker->randomElement([200, 201, 301, 302])
                : null,
            'response_time' => $status === 'online'
                ? $this->faker->numberBetween(50, 3000)
                : null,
            'error_message' => $status === 'offline'
                ? $this->faker->randomElement([
                    'Connection timed out',
                    'Could not resolve host',
                    'SSL certificate error',
                ])
                : null,
        ];
    }

    public function online(): static
    {
        return $this->state([
            'status'        => 'online',
            'response_code' => 200,
            'response_time' => $this->faker->numberBetween(50, 500),
            'error_message' => null,
        ]);
    }

    public function offline(): static
    {
        return $this->state([
            'status'        => 'offline',
            'response_code' => null,
            'response_time' => null,
            'error_message' => 'Connection timed out',
        ]);
    }
}
