<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'program' => fake()->words(3, true),
            'type' => 'University',
            'last_date' => now()->addMonth(),
        ];
    }
}
