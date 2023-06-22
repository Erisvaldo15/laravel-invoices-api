<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $paid = fake()->boolean();

        return [
            'user_id' => User::all('id')->random(),
            'type' => fake()->randomElement(['C', 'B', 'P']),
            'paid' => $paid,
            'value' => fake()->numberBetween(1000, 20000),
            'payment_date' => $paid ? fake()->randomElement([fake()->dateTimeThisMonth()]) : null
        ];
    }
}
