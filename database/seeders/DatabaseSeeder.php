<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@megaevent.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'status'   => 'active',
            'phone'    => '+216 20 123 456',
        ]);

        $organizer = User::create([
            'name'     => 'Adel Mcharek',
            'email'    => 'adel@megaevent.com',
            'password' => bcrypt('password'),
            'role'     => 'organisateur',
            'status'   => 'active',
            'phone'    => '+216 55 234 567',
        ]);

        $p1 = User::create([
            'name'     => 'Participant One',
            'email'    => 'participant1@megaevent.com',
            'password' => bcrypt('password'),
            'role'     => 'participant',
            'status'   => 'active',
            'phone'    => '+216 22 345 678',
        ]);

        $p2 = User::create([
            'name'     => 'Participant Two',
            'email'    => 'participant2@megaevent.com',
            'password' => bcrypt('password'),
            'role'     => 'participant',
            'status'   => 'active',
            'phone'    => '+216 98 456 789',
        ]);

        // Events
        $forum = Event::create([
            'user_id'     => $organizer->id,
            'title'       => 'Digital Leadership Forum',
            'description' => 'A full-day forum on digital transformation and leadership in the modern enterprise.',
            'category'    => 'professional',
            'date'        => now()->addDays(14),
            'location'    => 'Tunis Convention Center',
            'capacity'    => 200,
            'price'       => 0,
            'status'      => 'published',
            'visibility'  => 'public',
        ]);

        $fest = Event::create([
            'user_id'     => $organizer->id,
            'title'       => 'Summer Culture Fest',
            'description' => 'Music, art installations, and local gastronomy in the heart of the city.',
            'category'    => 'cultural',
            'date'        => now()->addDays(30),
            'location'    => 'Sfax Grand Theatre',
            'capacity'    => 500,
            'price'       => 15.00,
            'status'      => 'published',
            'visibility'  => 'public',
        ]);

        $innovation = Event::create([
            'user_id'     => $organizer->id,
            'title'       => 'Corporate Innovation Day',
            'description' => 'Workshops and keynotes for enterprise innovation teams.',
            'category'    => 'corporate',
            'date'        => now()->addDays(45),
            'location'    => 'Remote',
            'capacity'    => 120,
            'price'       => 0,
            'status'      => 'published',
            'visibility'  => 'public',
        ]);

        $marathon = Event::create([
            'user_id'     => $organizer->id,
            'title'       => 'City Marathon Expo',
            'description' => 'Expo hall and partner booths ahead of race weekend.',
            'category'    => 'cultural',
            'date'        => now()->addDays(60),
            'location'    => 'Tunis Sports Complex',
            'capacity'    => 800,
            'price'       => 0,
            'status'      => 'published',
            'visibility'  => 'public',
        ]);

        $wellness = Event::create([
            'user_id'     => $organizer->id,
            'title'       => 'Wellness & HR Roundtable',
            'description' => 'Closed-door session for HR leaders — awaiting admin validation.',
            'category'    => 'corporate',
            'date'        => now()->addDays(20),
            'location'    => 'Sousse Business Hub',
            'capacity'    => 80,
            'price'       => 0,
            'status'      => 'pending',
            'visibility'  => 'public',
        ]);

        // Registrations
        Registration::create([
            'user_id'       => $p1->id,
            'event_id'      => $forum->id,
            'status'        => 'confirmed',
            'registered_at' => now()->subDays(5),
        ]);

        Registration::create([
            'user_id'       => $p1->id,
            'event_id'      => $fest->id,
            'status'        => 'confirmed',
            'registered_at' => now()->subDays(3),
        ]);

        Registration::create([
            'user_id'       => $p2->id,
            'event_id'      => $forum->id,
            'status'        => 'confirmed',
            'registered_at' => now()->subDays(4),
        ]);

        Registration::create([
            'user_id'       => $p2->id,
            'event_id'      => $innovation->id,
            'status'        => 'confirmed',
            'registered_at' => now()->subDays(2),
        ]);

        Registration::create([
            'user_id'       => $p1->id,
            'event_id'      => $marathon->id,
            'status'        => 'confirmed',
            'registered_at' => now()->subDays(1),
        ]);
    }
}
