<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

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
        ])->assignRole('admin');

        User::create([
            'username' => 'Toe Aung Myin',
            'email' => 'toeaungmyin@ucsm.edu.mm',
            'birthday' => Carbon::parse('8-6-2003'),
            'profile_url' => '',
            'online' => false,
            'email_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('user');

        User::create([
            'username' => 'Hsu Cherry Linn',
            'email' => 'hsucherrylinn@ucsm.edu.mm',
            'birthday' => Carbon::parse('20-10-2003'),
            'profile_url' => '',
            'online' => false,
            'email_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('user');

        User::create([
            'username' => 'Seng Moon Jar',
            'email' => 'sengmoonjar@ucsm.edu.mm',
            'birthday' => Carbon::parse('20-10-2003'),
            'profile_url' => '',
            'online' => false,
            'email_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('user');

        User::create([
            'username' => 'Nay CHi Hlaing',
            'email' => 'naychihlaing@ucsm.edu.mm',
            'birthday' => Carbon::parse('20-10-2003'),
            'profile_url' => '',
            'online' => false,
            'email_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('user');

        $faker = Faker::create();

        // Create 20 users with random attributes
        for ($i = 0; $i < 20; $i++) {
            User::create([
                'username' => $faker->userName,
                'email' => $faker->userName . '@ucsm.edu.mm',
                'birthday' => $faker->date,
                'profile_url' => $faker->imageUrl(),
                'online' => $faker->boolean,
                'email_verified' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ])->assignRole('user');
        }
    }
}
