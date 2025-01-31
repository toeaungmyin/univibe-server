<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

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
            'profile_url' => 'uploads/profiles/logo.png',
                'email_verified' => 1,
                'email_verified_at' => Carbon::now(),
        ])->assignRole('admin');

        User::create([
            'username' => 'Toe Aung Myin',
            'email' => 'toeaungmyin.02@ucsm.edu.mm',
            'birthday' => Carbon::parse('8-6-2003'),
            'profile_url' => 'uploads/profiles/sta.jpg',
            'online' => false,
            'email_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('user');

        User::create([
            'username' => 'Hsu Cherry Linn',
            'email' => 'hsucherrylinn@ucsm.edu.mm',
            'birthday' => Carbon::parse('20-10-2003'),
            'profile_url' => "uploads/profiles/hsu.jpg",
            'online' => false,
            'email_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('user');

        User::create([
            'username' => 'Seng Moon Jar',
            'email' => 'sengmoonjar@ucsm.edu.mm',
            'birthday' => Carbon::parse('20-10-2003'),
            'profile_url' => "uploads/profiles/seng.jpg",
            'online' => false,
            'email_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('user');

        User::create([
            'username' => 'Nay CHi Hlaing',
            'email' => 'naychihlaing@ucsm.edu.mm',
            'birthday' => Carbon::parse('20-10-2003'),
            'profile_url' => "uploads/profiles/nay.jpg",
            'online' => false,
            'email_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('user');

        User::create([
            'username' => 'Mg Bo Thi',
            'email' => 'thihazaw2@ucsm.edu.mm',
            'birthday' => Carbon::parse('20-10-2003'),
            'profile_url' => "uploads/profiles/3.jpg",
            'online' => false,
            'email_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('user');

        User::create([
            'username' => 'Nay Ye Linn',
            'email' => 'nayyelinn@ucsm.edu.mm',
            'birthday' => Carbon::parse('20-10-2003'),
            'profile_url' => "uploads/profiles/3.jpg",
            'online' => false,
            'email_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('user');

        $faker = Faker::create();
        $currentYear = date('Y');
        $startDate = $currentYear . '-08-01'; // Start of the year
        $endDate = date('Y-m-d');
        // Create 20 users with random attributes
        for ($i = 0; $i < 20; $i++) {
            $image = $i + 1;
            User::create([
                'username' => $faker->userName,
                'email' => $faker->userName . '@ucsm.edu.mm',
                'birthday' => $faker->date,
                'profile_url' => "uploads/profiles/" . $image . ".jpg",
                'online' => false,
                'email_verified' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => $faker->dateTimeBetween($startDate, $endDate),
            ])->assignRole('user');
        }
    }
}
