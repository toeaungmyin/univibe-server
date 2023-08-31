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
        $users = User::role('user')->get();

        foreach ($users as $user) {
            // Randomly select a user to follow
            $followings = User::role('user')->where('id', '!=', $user->id)->take(3)->get();

            foreach ($followings as $following) {
                // Skip if the user is already following the selected user or if they are the same user
                if ($user->id === $following->id || $user->followings->contains('following_id', $following->id)) {
                    continue;
                }

                // Create a new following relationship
                if (rand(0, 1)) {
                    Following::create([
                        'follower_id' => $user->id,
                        'following_id' => $following->id,
                    ]);
                }

                // Randomly establish friendship
                if (rand(0, 1)) {
                    Following::create([
                        'follower_id' => $following->id,
                        'following_id' => $user->id,
                    ]);
                }
            }

        }
    }
}
