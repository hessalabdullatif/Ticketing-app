<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
{
    return [
        'event_id' => Event::factory(),
        'type' => fake()->randomElement(['General', 'VIP', 'Backstage']),
        'price' => fake()->randomFloat(2, 50, 500),
        'quantity_available' => fake()->numberBetween(10, 200),
    ];
}
}
