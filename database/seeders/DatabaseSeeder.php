<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::create([
            'username' => 'Admin',
            'mobile_number' => '01234567891',
            'password' => Hash::make('password'),
            'type' => 'admin',
            'is_verified' => true,
        ]);
    }
}
