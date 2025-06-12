<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create regular users
        $users = User::factory(10)->create();

        // Create posts
        $allUsers = User::all();
        foreach ($allUsers as $user) {
            Post::factory(rand(1, 5))->create(['user_id' => $user->id]);
        }

        // Create comments
        $posts = Post::all();
        foreach ($posts as $post) {
            Comment::factory(rand(0, 3))->create([
                'post_id' => $post->id,
                'user_id' => $allUsers->random()->id,
            ]);
        }
    }
}