<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => fake()->paragraphs(rand(1, 3), true),
            'privacy' => fake()->randomElement(['public', 'friends', 'private']),
            'is_active' => true,
            'likes_count' => fake()->numberBetween(0, 100),
            'comments_count' => fake()->numberBetween(0, 20),
        ];
    }
}