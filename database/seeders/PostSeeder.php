<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get all user IDs assuming you have users
        $userIds = User::role('user')->pluck('id')->toArray();

        for ($i = 0; $i < 50; $i++) {
            $post = new Post([
                'user_id' => $faker->randomElement($userIds),
                'content' => $faker->paragraph,
                'image' => $faker->imageUrl(), // Assuming you want to add image URLs
                'audience' => 'public',
            ]);

            $post->save();
        }
    }
}
