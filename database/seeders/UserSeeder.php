<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin =
            User::create([
                'username' => 'Team UniVibe',
                'email' => 'team.univibe@gmail.com',
                'password' => Hash::make('admin'),
                'birthday' => Carbon::now()->startOfMonth(),
                'email_verified' => 1,
                'email_verified_at' => Carbon::now(),
            ]);
        $admin->assignRole('admin');
    }
}
