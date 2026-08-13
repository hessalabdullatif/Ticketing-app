<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
// step 1 
//A Factory is a file that tells Laravel "when I ask you for fake data for this model, generate random data in this shape.
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
{
    static $names = null;

    if ($names === null) {
        $names = collect([
            'Summer Music Festival',
            'Tech Innovators Conference',
            'Riyadh Comedy Night',
            'International Food Expo',
            'Startup Pitch Day',
            'Jazz & Blues Evening',
            'Gaming Championship Finals',
            'Art & Design Exhibition',
            'Fitness & Wellness Expo',
            'Stand-up Comedy Tour',
        ])->shuffle()->values();
    }

    static $index = 0;
    $name = $names[$index % $names->count()];
    $index++;
    $descriptions = [
        'Join us for an unforgettable night of live music and entertainment.',
        'A gathering of industry leaders discussing the future of technology.',
        'An evening full of laughter with top comedians from around the region.',
        'Explore the latest in cuisine, culture, and culinary innovation.',
        'Watch emerging startups pitch their ideas to top investors.',
    ];

    return [
        'name' => $name,
        'description' => fake()->randomElement($descriptions),
        'venue' => fake()->company() . ' Arena',
        'city' => fake()->randomElement(['Riyadh', 'Jeddah', 'Dammam', 'Dubai']),
        'starts_at' => fake()->dateTimeBetween('now', '+2 months'),
    ];
}
}

