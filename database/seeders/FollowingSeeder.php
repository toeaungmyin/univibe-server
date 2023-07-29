<?php

namespace Database\Seeders;

use App\Models\Following;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FollowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Randomly select a user to follow
            $followingUser = $users->random();

            // Skip if the user is already following the selected user or if they are the same user
            if ($user->id === $followingUser->id || $user->followings->contains('following_id', $followingUser->id)) {
                continue;
            }

            // Create a new following relationship
            Following::create([
                'follower_id' => $user->id,
                'following_id' => $followingUser->id,
            ]);

            // Randomly establish friendship
            if (rand(0, 1)) {
                Following::create([
                    'follower_id' => $followingUser->id,
                    'following_id' => $user->id,
                ]);
            }
        }
    }
}
