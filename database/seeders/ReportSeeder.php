<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get 20 random users and posts
        $userIds = User::role('user')->inRandomOrder()->limit(20)->get()->pluck('id');
        $posts = Post::inRandomOrder()->limit(20)->get();
        $titles = [
            "Report Bully",
            "Stop Cyberbullying",
            "Flag Harassment",
            "Report Troll",
            "End Hate",
            "Flag Abuse",
            "Stop Harassment",
            "Block Troll",
            "Fight Cyberbullying",
            "Report Abuser",
            "Block Hate",
            "Stand Up Against Bullies",
        ];

        for ($i = 0; $i < 50; $i++) {
            UserReport::create([
                'compliant_id' => $faker->randomElement($userIds),
                'resistant_id' => $faker->randomElement($userIds),
                'title' => $titles[array_rand($titles)],
                'description' => $faker->paragraph,
            ]);
        }

        foreach ($posts as $post) {
            PostReport::create([
                'compliant_id' => $faker->randomElement($userIds),
                'resistant_id' => $post->user->id,
                'post_id' => $post->id,
                'title' => $titles[array_rand($titles)],
                'description' => $faker->paragraph,
            ]);
        }
    }
}
