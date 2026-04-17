<?php

use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Organizer\EventController as OrganizerEventController;
use App\Http\Controllers\Organizer\ProfileController;
use App\Http\Controllers\Participant\RegistrationController;
use App\Models\Event;
use App\Models\Partner;
use App\Models\Registration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ─── Public ──────────────────────────────────────────────────────────────────

Route::get('/', function () {
    $featuredEvents = Event::where('status', 'published')
        ->where('visibility', 'public')
        ->withCount('registrations')
        ->latest('date')
        ->take(3)
        ->get();

    return view('welcome', compact('featuredEvents'));
})->name('home');

Route::get('/events', function (Request $request) {
    $query = Event::where('status', 'published')
        ->where('visibility', 'public')
        ->withCount('registrations');

    if ($request->filled('search')) {
        $s = $request->search;
        $query->where(fn ($q) =>
            $q->where('title', 'like', "%{$s}%")
              ->orWhere('description', 'like', "%{$s}%")
        );
    }

    if ($request->filled('location')) {
        $query->where('location', 'like', '%' . $request->location . '%');
    }

    if ($request->filled('date_from')) {
        $query->where('date', '>=', Carbon::parse($request->date_from)->startOfDay());
    }

    if ($request->filled('date_to')) {
        $query->where('date', '<=', Carbon::parse($request->date_to)->endOfDay());
    }

    if ($request->filled('category') && $request->category !== 'all') {
        $query->where('category', strtolower($request->category));
    }

    if ($request->filled('mode') && $request->mode !== 'all') {
        if ($request->mode === 'online') {
            $query->whereRaw('LOWER(location) = ?', ['remote']);
        } else {
            $query->whereRaw('LOWER(location) != ?', ['remote']);
        }
    }

    match ($request->get('sort', 'date_asc')) {
        'date_desc'    => $query->orderByDesc('date'),
        'capacity_asc' => $query->orderBy('capacity'),
        'title_asc'    => $query->orderBy('title'),
        default        => $query->orderBy('date'),
    };

    $events = $query->get();

    return view('events.index', compact('events'));
})->name('events.index');

Route::get('/events/{id}', function (int $id) {
    $event = Event::with(['partners', 'registrations'])->findOrFail($id);

    if ($event->status !== 'published' && (! auth()->check() || ! auth()->user()->isAdmin())) {
        abort(404);
    }

    return view('events.show', [
        'event'             => $event,
        'agenda'            => [],
        'partners'          => $event->partners,
        'participantsCount' => $event->registrations->where('status', '!=', 'cancelled')->count(),
    ]);
})->name('events.show');

// ─── Auth ─────────────────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ─── Organizer ────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:organisateur'])->prefix('organizer')->name('organizer.')->group(function () {

    Route::get('/dashboard', function () {
        $userId = auth()->id();

        $totalEvents      = Event::where('user_id', $userId)->count();
        $totalParticipants = Registration::whereHas('event', fn ($q) => $q->where('user_id', $userId))
            ->where('status', '!=', 'cancelled')
            ->count();
        $pendingEvents    = Event::where('user_id', $userId)->where('status', 'pending')->count();
        $publishedEvents  = Event::where('user_id', $userId)->where('status', 'published')->count();

        $recentEvents = Event::where('user_id', $userId)
            ->withCount(['registrations' => fn ($q) => $q->where('status', '!=', 'cancelled')])
            ->latest()
            ->take(5)
            ->get();

        return view('organizer.dashboard', [
            'stats' => [
                'total_events'      => $totalEvents,
                'total_participants' => $totalParticipants,
                'pending_events'    => $pendingEvents,
                'published_events'  => $publishedEvents,
            ],
            'recentEvents' => $recentEvents,
        ]);
    })->name('dashboard');

    Route::resource('events', OrganizerEventController::class)->except(['show']);
    Route::get('events/{id}/participants', [OrganizerEventController::class, 'participants'])->name('events.participants');

    Route::get('/participants', function () {
        return view('organizer.participants');
    })->name('participants');

    Route::get('/partners', function () {
        return view('organizer.partners');
    })->name('partners');

    Route::get('/profile', function () {
        return view('organizer.profile');
    })->name('profile');

    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ─── Admin ────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        $pendingEvents = Event::where('status', 'pending')
            ->with('organiser')
            ->latest()
            ->get();

        $recentRegistrations = Registration::with(['user', 'event'])
            ->latest('registered_at')
            ->take(5)
            ->get();

        // Events per month (current year) for bar chart
        $monthlyEvents = Event::selectRaw("CAST(strftime('%m', date) AS INTEGER) as month, count(*) as total")
            ->whereYear('date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartMonths      = [];
        $chartMonthValues = [];
        for ($m = 1; $m <= 12; $m++) {
            if (isset($monthlyEvents[$m])) {
                $chartMonths[]      = $monthNames[$m - 1];
                $chartMonthValues[] = $monthlyEvents[$m]->total;
            }
        }
        if (empty($chartMonths)) {
            $chartMonths      = array_slice($monthNames, 0, 6);
            $chartMonthValues = array_fill(0, 6, 0);
        }

        // Events by category for pie chart
        $categoryData = Event::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();
        $pieLabels = $categoryData->pluck('category')->map(fn ($c) => ucfirst($c))->values()->toArray();
        $pieValues = $categoryData->pluck('total')->values()->toArray();

        return view('admin.dashboard', [
            'stats' => [
                'events'   => Event::count(),
                'users'    => User::count(),
                'pending'  => $pendingEvents->count(),
                'partners' => Partner::count(),
            ],
            'pendingEvents'       => $pendingEvents,
            'recentRegistrations' => $recentRegistrations,
            'chartMonths'         => $chartMonths,
            'chartMonthValues'    => $chartMonthValues,
            'pieLabels'           => $pieLabels,
            'pieValues'           => $pieValues,
        ]);
    })->name('dashboard');

    Route::resource('events', AdminEventController::class)->except(['create', 'store', 'edit', 'update']);
    Route::post('events/{id}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::post('events/{id}/reject', [AdminEventController::class, 'reject'])->name('events.reject');

    Route::get('/users', function () {
        return view('admin.users.index', [
            'users' => User::orderBy('name')->get(),
        ]);
    })->name('users.index');

    Route::get('/statistics', function () {
        $totalEvents        = Event::count();
        $totalRegistrations = Registration::count();

        $allEvents  = Event::withCount('registrations')->get();
        $avgFillRate = $allEvents->avg(fn ($e) =>
            $e->capacity > 0 ? ($e->registrations_count / $e->capacity) * 100 : 0
        );

        $topCategory = Event::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        // Registrations per month (current year)
        $regsByMonth = Registration::selectRaw("CAST(strftime('%m', registered_at) AS INTEGER) as month, count(*) as total")
            ->whereYear('registered_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $lineLabels = [];
        $lineValues = [];
        for ($m = 1; $m <= 12; $m++) {
            $lineLabels[] = $monthNames[$m - 1];
            $lineValues[] = isset($regsByMonth[$m]) ? $regsByMonth[$m]->total : 0;
        }

        $topEvents = Event::withCount('registrations')
            ->orderByDesc('registrations_count')
            ->take(5)
            ->get();

        return view('admin.statistics.index', [
            'kpis' => [
                'events'           => (string) $totalEvents,
                'registrations'    => (string) $totalRegistrations,
                'avg_fill_rate'    => round($avgFillRate, 1) . '%',
                'top_category'     => $topCategory ? ucfirst($topCategory->category) : '—',
            ],
            'lineLabels' => $lineLabels,
            'lineValues' => $lineValues,
            'topEvents'  => $topEvents,
        ]);
    })->name('statistics.index');

    Route::get('/partners', function () {
        return view('admin.partners');
    })->name('partners');
});

// ─── Participant ──────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:participant'])->prefix('participant')->name('participant.')->group(function () {

    Route::get('/dashboard', function () {
        $userId = auth()->id();

        $registeredEventIds = Registration::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->pluck('event_id');

        $upcoming = Event::whereIn('id', $registeredEventIds)
            ->where('date', '>', now())
            ->withCount('registrations')
            ->orderBy('date')
            ->get();

        $history = Registration::where('user_id', $userId)
            ->with('event')
            ->latest('registered_at')
            ->get();

        // Recommended events
        $preferredCategories = Event::whereIn('id', $registeredEventIds)->pluck('category')->unique();

        $recommended = Event::where('status', 'published')
            ->whereNotIn('id', $registeredEventIds)
            ->when($preferredCategories->isNotEmpty(), fn ($q) =>
                $q->whereIn('category', $preferredCategories)
            )
            ->withCount('registrations')
            ->orderByDesc('registrations_count')
            ->take(3)
            ->get();

        return view('participant.dashboard', compact('upcoming', 'history', 'recommended'));
    })->name('dashboard');

    Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');

    Route::get('/profile', function () {
        return view('participant.profile');
    })->name('profile');
});

// ─── Registration actions (auth, any role) ────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::post('events/{id}/register', [RegistrationController::class, 'store'])->name('events.register');
    Route::delete('registrations/{id}', [RegistrationController::class, 'destroy'])->name('registrations.cancel');
});
