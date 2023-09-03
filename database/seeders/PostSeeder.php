<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

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
        $currentYear = date('Y');
        $startDate = $currentYear . '-08-01'; // Start of the year
        $endDate = date('Y-m-d');
        // Get all user IDs assuming you have users
        $userIds = User::role('user')->pluck('id')->toArray();
        $disk = 'public';
        $imageDirectory = 'uploads/images';
        for ($i = 0; $i < 100; $i++) {
            $audienceOptions = ['public', 'private', 'friends'];
            $randomAudienceIndex = array_rand($audienceOptions);
            $imageFiles = Storage::disk($disk)->files($imageDirectory);
            $randomImage = $faker->randomElement($imageFiles);
            $post = new Post([
                'user_id' => $faker->randomElement($userIds),
                'content' => $faker->paragraph,'image' => $randomImage,
                'audience' => $audienceOptions[$randomAudienceIndex], // Use the selected value
                'created_at' => $faker->dateTimeBetween($startDate, $endDate),
            ]);

            $post->save();
            for ($j = 0; $j < random_int(1, 5); $j++) {
                Comment::create([
                    'user_id' => $faker->randomElement($userIds), // Assuming you have user authentication
                    'post_id' => $post->id,
                    'comment' => $faker->sentence,
                ]);
            }

            for ($k = 0; $k < random_int(5, 20); $k++) {
                Reaction::create([
                    'user_id' => $faker->randomElement($userIds),
                    'post_id' => $post->id,
                ]);
            }

        }
    }
}
