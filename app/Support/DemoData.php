<?php

namespace App\Support;

use Illuminate\Support\Collection;

class DemoData
{
    /**
     * @return Collection<int, object>
     */
    public static function events(): Collection
    {
        return collect([
            (object) [
                'id' => 1,
                'title' => 'Digital Leadership Forum',
                'date' => now()->addDays(14),
                'location' => 'Paris Convention Center',
                'category' => 'Professional',
                'capacity' => 200,
                'registered' => 142,
                'status' => 'OPEN',
                'description' => 'A full-day forum on digital transformation and leadership.',
                'organizer_name' => 'Jane Organizer',
                'created_at' => now()->subDays(3),
            ],
            (object) [
                'id' => 2,
                'title' => 'Summer Culture Fest',
                'date' => now()->addDays(30),
                'location' => 'Lyon Grand Theatre',
                'category' => 'Cultural',
                'capacity' => 500,
                'registered' => 500,
                'status' => 'FULL',
                'description' => 'Music, art installations, and local gastronomy.',
                'organizer_name' => 'Culture Co.',
                'created_at' => now()->subDays(10),
            ],
            (object) [
                'id' => 3,
                'title' => 'Corporate Innovation Day',
                'date' => now()->addDays(45),
                'location' => 'Remote',
                'category' => 'Corporate',
                'capacity' => 120,
                'registered' => 40,
                'status' => 'OPEN',
                'description' => 'Workshops and keynotes for enterprise innovation teams.',
                'organizer_name' => 'Jane Organizer',
                'created_at' => now()->subDays(1),
            ],
            (object) [
                'id' => 4,
                'title' => 'City Marathon Expo',
                'date' => now()->addDays(60),
                'location' => 'Marseille',
                'category' => 'Cultural',
                'capacity' => 800,
                'registered' => 120,
                'status' => 'COMING SOON',
                'description' => 'Expo hall and partner booths ahead of race weekend.',
                'organizer_name' => 'Run Club',
                'created_at' => now()->subDays(2),
            ],
            (object) [
                'id' => 5,
                'title' => 'Wellness & HR Roundtable',
                'date' => now()->addDays(20),
                'location' => 'Bordeaux',
                'category' => 'Corporate',
                'capacity' => 80,
                'registered' => 0,
                'status' => 'PENDING',
                'description' => 'Closed-door session for HR leaders — awaiting admin validation.',
                'organizer_name' => 'Jane Organizer',
                'created_at' => now()->subHours(6),
            ],
        ]);
    }

    public static function filterEvents(\Illuminate\Http\Request $request): Collection
    {
        $q = self::events();
        if ($request->filled('search')) {
            $s = strtolower($request->string('search'));
            $q = $q->filter(fn ($e) => str_contains(strtolower($e->title), $s));
        }
        if ($request->filled('location')) {
            $s = strtolower($request->string('location'));
            $q = $q->filter(fn ($e) => str_contains(strtolower($e->location), $s));
        }
        if ($request->filled('date')) {
            $d = $request->date('date');
            $q = $q->filter(fn ($e) => \Carbon\Carbon::parse($e->date)->startOfDay()->gte($d));
        }
        $cat = $request->string('category');
        if ($cat->isNotEmpty() && $cat->toString() !== 'all') {
            $q = $q->filter(fn ($e) => $e->category === $cat->toString());
        }

        return $q->values();
    }

    public static function findEvent(int $id): ?object
    {
        return self::events()->firstWhere('id', $id);
    }
}
