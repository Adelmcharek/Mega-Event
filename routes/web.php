<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\User;
use App\Support\DemoData;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'featuredEvents' => DemoData::events()->take(3),
    ]);
})->name('home');

Route::get('/events', function (Illuminate\Http\Request $request) {
    return view('events.index', [
        'events' => DemoData::filterEvents($request),
    ]);
})->name('events.index');

Route::get('/events/{id}', function (int $id) {
    $event = DemoData::findEvent($id);
    abort_unless($event, 404);

    return view('events.show', [
        'event' => $event,
        'agenda' => [
            (object) ['time' => '09:00', 'label' => 'Check-in & coffee'],
            (object) ['time' => '10:30', 'label' => 'Keynotes & panels'],
            (object) ['time' => '14:00', 'label' => 'Networking'],
        ],
        'partners' => ['MEGA PIXEL', 'Innovate France', 'Urban Media'],
        'participantsCount' => $event->registered ?? 0,
    ]);
})->name('events.show');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', function () {
        $events = DemoData::events();

        return view('organizer.dashboard', [
            'stats' => [
                'total_events' => $events->count(),
                'total_participants' => $events->sum(fn ($e) => $e->registered ?? 0),
                'pending_events' => $events->where('status', 'PENDING')->count(),
                'published_events' => $events->filter(fn ($e) => in_array($e->status, ['OPEN', 'FULL', 'COMING SOON'], true))->count(),
            ],
            'recentEvents' => $events->take(5),
        ]);
    })->name('dashboard');

    Route::get('/events', function () {
        return view('organizer.events.index', ['events' => DemoData::events()]);
    })->name('events.index');

    Route::get('/events/create', function () {
        return view('organizer.events.create');
    })->name('events.create');

    Route::post('/events', function () {
        return redirect()->route('organizer.events.index')->with('success', 'Event submitted for review (demo).');
    })->name('events.store');

    Route::get('/events/{id}/edit', function (int $id) {
        $event = DemoData::findEvent($id);
        abort_unless($event, 404);

        return view('organizer.events.edit', ['event' => $event]);
    })->name('events.edit');

    Route::put('/events/{id}', function (int $id) {
        return redirect()->route('organizer.events.index')->with('success', 'Event updated (demo).');
    })->name('events.update');

    Route::delete('/events/{id}', function (int $id) {
        return redirect()->route('organizer.events.index')->with('success', 'Event removed (demo).');
    })->name('events.destroy');

    Route::get('/events/{id}/participants', function (int $id) {
        $event = DemoData::findEvent($id);
        abort_unless($event, 404);

        return view('organizer.events.participants', [
            'event' => $event,
            'eventName' => $event->title,
            'participants' => [
                (object) ['name' => 'Alex Martin', 'email' => 'alex@example.com', 'registered_at' => now()->subDay(), 'status' => 'CONFIRMED'],
                (object) ['name' => 'Sam Dupont', 'email' => 'sam@example.com', 'registered_at' => now()->subDays(2), 'status' => 'PENDING'],
            ],
            'stats' => [
                'total' => 2,
                'spots_left' => max(0, ($event->capacity ?? 0) - ($event->registered ?? 0)),
                'attendance' => 88,
            ],
        ]);
    })->name('events.participants');

    Route::get('/participants', function () {
        return view('organizer.participants');
    })->name('participants');

    Route::get('/partners', function () {
        return view('organizer.partners');
    })->name('partners');

    Route::get('/profile', function () {
        return view('organizer.profile');
    })->name('profile');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $events = DemoData::events();
        $pending = $events->where('status', 'PENDING')->values();

        return view('admin.dashboard', [
            'stats' => [
                'events' => $events->count(),
                'users' => User::query()->count(),
                'pending' => $pending->count(),
                'partners' => 12,
            ],
            'pendingEvents' => $pending,
            'recentRegistrations' => [
                (object) ['user' => 'Alex Martin', 'event' => 'Digital Leadership Forum', 'date' => now()->format('M d, Y')],
                (object) ['user' => 'Sam Dupont', 'event' => 'Corporate Innovation Day', 'date' => now()->subDay()->format('M d, Y')],
            ],
            'chartMonths' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'chartMonthValues' => [4, 7, 5, 12, 9, 14],
            'pieLabels' => ['Professional', 'Cultural', 'Corporate'],
            'pieValues' => [45, 30, 25],
        ]);
    })->name('dashboard');

    Route::get('/events', function () {
        return view('admin.events.index', ['events' => DemoData::events()]);
    })->name('events.index');

    Route::get('/events/{id}', function (int $id) {
        $event = DemoData::findEvent($id);
        abort_unless($event, 404);

        return view('admin.events.show', ['event' => $event]);
    })->name('events.show');

    Route::post('/events/{id}/approve', function (int $id) {
        return redirect()->route('admin.events.index')->with('success', 'Event approved (demo).');
    })->name('events.approve');

    Route::post('/events/{id}/reject', function (int $id) {
        return redirect()->route('admin.events.index')->with('warning', 'Event rejected (demo).');
    })->name('events.reject');

    Route::delete('/events/{id}', function (int $id) {
        return redirect()->route('admin.events.index')->with('success', 'Event deleted (demo).');
    })->name('events.destroy');

    Route::get('/users', function () {
        return view('admin.users.index', [
            'users' => User::query()->orderBy('name')->get(),
        ]);
    })->name('users.index');

    Route::get('/statistics', function () {
        $events = DemoData::events();

        return view('admin.statistics.index', [
            'kpis' => [
                'events' => (string) $events->count(),
                'avg_participants' => (string) round($events->avg(fn ($e) => $e->registered ?? 0), 1),
                'top_location' => $events->groupBy('location')->sortByDesc(fn ($g) => $g->sum('registered'))->keys()->first() ?? '—',
                'busiest_month' => 'June',
            ],
            'lineLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'lineValues' => [120, 190, 150, 220, 280, 310],
            'topEvents' => $events->sortByDesc(fn ($e) => $e->registered ?? 0)->take(5)->values(),
        ]);
    })->name('statistics.index');

    Route::get('/partners', function () {
        return view('admin.partners');
    })->name('partners');
});

Route::middleware(['auth', 'role:participant'])->prefix('participant')->name('participant.')->group(function () {
    Route::get('/dashboard', function () {
        $upcoming = DemoData::events()->whereIn('status', ['OPEN', 'COMING SOON'])->take(3)->values();
        $history = [
            (object) [
                'event_title' => 'Digital Leadership Forum',
                'event_date' => now()->addDays(14),
                'status' => 'CONFIRMED',
            ],
            (object) [
                'event_title' => 'Past Meetup',
                'event_date' => now()->subDays(10),
                'status' => 'CONFIRMED',
            ],
        ];

        return view('participant.dashboard', [
            'upcoming' => $upcoming,
            'history' => $history,
        ]);
    })->name('dashboard');

    Route::get('/registrations', function () {
        $registrations = [
            (object) [
                'event_title' => 'Digital Leadership Forum',
                'event_date' => now()->addDays(14),
                'registered_at' => now()->subDays(3),
                'status' => 'CONFIRMED',
            ],
            (object) [
                'event_title' => 'Summer Culture Fest',
                'event_date' => now()->addDays(30),
                'registered_at' => now()->subWeek(),
                'status' => 'PENDING',
            ],
        ];

        return view('participant.registrations.index', [
            'registrations' => $registrations,
        ]);
    })->name('registrations.index');

    Route::get('/profile', function () {
        return view('participant.profile');
    })->name('profile');
});
