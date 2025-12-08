<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

//seeds the database with initial test data
class DatabaseSeeder extends Seeder
{
    //seed the application's database
    
    public function run(): void
    {
        //create test user if not exists (safe to run multiple times)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password']
        );
    }
}