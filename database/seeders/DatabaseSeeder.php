<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            [
                'name'      => 'Dev',
                'user_name' => 'dev',
                'role' => 'admin',
                'can_add_store' => 1,
            ],
            [
                'email'    => 'dev@leadingsoft.eu',
                'password' => '123qwe123',
            ]);
    }
}
