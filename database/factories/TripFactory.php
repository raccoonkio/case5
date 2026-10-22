<?php

namespace Database\Factories;

use App\Trip;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Trip> */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return ['user_id' => User::factory(), 'title' => fake()->sentence(4), 'location' => 'Алтай, Россия', 'budget' => 42000, 'body' => fake()->paragraph(), 'places' => ['Чуйский тракт', 'Гейзерное озеро'], 'photo_path' => 'assets/altai.jpg'];
    }
}
