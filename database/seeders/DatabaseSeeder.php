<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@megaevent.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'organizer@megaevent.test'],
            [
                'name' => 'Jane Organizer',
                'password' => Hash::make('password'),
                'role' => 'organizer',
                'is_active' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'participant@megaevent.test'],
            [
                'name' => 'Alex Participant',
                'password' => Hash::make('password'),
                'role' => 'participant',
                'is_active' => true,
            ]
        );
    }
}
