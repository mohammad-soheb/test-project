<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'phone' => '987654321' . $i-1,
                'description' => 'Description for user ' . $i,
                'role_id' => rand(1, 10), 
                'profile_image' => 'default.jpg', 
            ]);
        }
    }
}
