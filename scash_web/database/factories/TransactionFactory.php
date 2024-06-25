<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'wallet_id' => Str::random(10),
            'amount' => fake()->randomFloat(2, 10, 500),
            'fee' => fake()->randomFloat(2, 10, 100),
            'created_at' => fake()->dateTimeBetween('-120 days', 'now'),
        ];
    }

}
