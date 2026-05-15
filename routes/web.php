<?php

use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Organizer\EventController as OrganizerEventController;
use App\Http\Controllers\Organizer\ProfileController as OrganizerProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Participant\RegistrationController;
use App\Services\RecommenderService;
use App\Models\Event;
use App\Models\Partner;
use App\Models\Registration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════
// CHANGEUR DE LANGUE — Commutation fr / en
// ═══════════════════════════════════════════

Route::get('/language/{lang}', [LanguageController::class, 'switch'])->name('language.switch');

// ═══════════════════════════════════════════
// ROUTES PUBLIQUES — Accessibles sans connexion
// ═══════════════════════════════════════════

// Page d'accueil — affiche les 3 prochains événements publiés et publics
Route::get('/', function () {
    $featuredEvents = Event::where('status', 'published')
        ->where('visibility', 'public')
        ->withCount('registrations')
        ->orderBy('date', 'asc')
        ->take(3)
        ->get();

    return view('welcome', compact('featuredEvents'));
})->name('home');

// Catalogue des événements avec filtres (recherche, lieu, date, catégorie, mode)
Route::get('/events', function (Request $request, RecommenderService $recommender) {
    $query = Event::where('status', 'published')
        ->where('visibility', 'public')
        ->withCount('registrations');

    // Filtre par mot-clé dans le titre ou la description
    if ($request->filled('search')) {
        $s = $request->search;
        $query->where(fn ($q) =>
            $q->where('title', 'like', "%{$s}%")
              ->orWhere('description', 'like', "%{$s}%")
        );
    }

    // Filtre par lieu (recherche partielle)
    if ($request->filled('location')) {
        $query->where('location', 'like', '%' . $request->location . '%');
    }

    // Filtre par plage de dates
    if ($request->filled('date_from')) {
        $query->where('date', '>=', Carbon::parse($request->date_from)->startOfDay());
    }

    if ($request->filled('date_to')) {
        $query->where('date', '<=', Carbon::parse($request->date_to)->endOfDay());
    }

    // Filtre par catégorie
    if ($request->filled('category') && $request->category !== 'all') {
        $query->where('category', strtolower($request->category));
    }

    // Filtre par mode : en ligne (mots-clés dans location) ou présentiel
    if ($request->filled('mode') && $request->mode !== 'all') {
        if ($request->mode === 'online') {
            $query->where(function ($q) {
                $q->where('location', 'like', '%online%')
                  ->orWhere('location', 'like', '%remote%')
                  ->orWhere('location', 'like', '%virtual%');
            });
        } elseif ($request->mode === 'onsite') {
            $query->where('location', 'not like', '%online%')
                  ->where('location', 'not like', '%remote%')
                  ->where('location', 'not like', '%virtual%');
        }
    }

    // Tri des résultats selon le paramètre 'sort'
    match ($request->get('sort', 'date_asc')) {
        'date_desc' => $query->orderByDesc('date'),
        'alpha_asc' => $query->orderBy('title', 'asc'),
        'capacity'  => $query->orderBy('capacity', 'desc'),
        default     => $query->orderBy('date'),
    };

    $allEvents = $query->get();

    // Séparation des événements à venir et passés pour l'affichage dans deux sections
    $today = Carbon::today();
    $upcomingEvents = $allEvents->filter(fn($e) => Carbon::parse($e->date)->endOfDay()->greaterThanOrEqualTo($today));
    $pastEvents     = $allEvents->filter(fn($e) => Carbon::parse($e->date)->endOfDay()->lessThan($today));

    $recommendedEvents = collect();
    if (auth()->check()) {
        $recommendations = $recommender->getRecommendations(auth()->id(), 3);
        $recommendedEvents = collect($recommendations)->map(function ($item) {
            $event = \App\Models\Event::find($item['id']);
            if ($event) {
                $event->recommendation_reason = $item['reason'];
                $event->recommendation_score  = $item['score'];
            }
            return $event;
        })->filter();
    }

    return view('events.index', compact('upcomingEvents', 'pastEvents', 'recommendedEvents'));
})->name('events.index');

// Détail d'un événement — accessible publiquement si publié, ou par l'admin pour tous statuts
Route::get('/events/{id}', function (int $id) {
    $event = Event::with(['organiser', 'partners', 'registrations'])->findOrFail($id);

    // Les événements non publiés ne sont visibles que par l'administrateur
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

// ═══════════════════════════════════════════
// ROUTES D'AUTHENTIFICATION
// ═══════════════════════════════════════════

// Routes protégées par le middleware 'guest' : inaccessibles si déjà connecté
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

// Déconnexion — nécessite d'être connecté, méthode POST pour sécurité CSRF
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Vérification d'adresse email (MustVerifyEmail)
// Notice : page informant l'utilisateur de vérifier son email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Confirmation du lien de vérification (lien signé envoyé par email)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    $user = auth()->user();
    return redirect()->route(match($user->role) {
        'admin'        => 'admin.dashboard',
        'organisateur' => 'organizer.dashboard',
        default        => 'participant.dashboard',
    })->with('success', 'Email verified successfully! Welcome to MEGA EVENT!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Renvoi du lien de vérification — limité à 6 tentatives par minute (throttle)
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Verification link sent! Check your inbox.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ═══════════════════════════════════════════
// ROUTES ORGANISATEUR — Rôle : organisateur
// ═══════════════════════════════════════════

// Groupe protégé par 'auth' + 'role:organisateur', préfixe URL /organizer
Route::middleware(['auth', 'role:organisateur'])->prefix('organizer')->name('organizer.')->group(function () {

    // Tableau de bord : statistiques + graphiques des événements de l'organisateur
    Route::get('/dashboard', function (RecommenderService $recommender) {
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

        // Graphique 1 : inscriptions par mois sur les 6 derniers mois (compatible SQLite)
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();
        $regsByMonth = Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, CAST(strftime('%Y', created_at) AS INTEGER) as year, COUNT(*) as total")
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $frenchMonths = ['', 'Janv', 'Févr', 'Mars', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
        $chartLabels = [];
        $chartValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $chartLabels[] = $frenchMonths[(int)$d->format('m')];
            $row = $regsByMonth->first(fn($r) => $r->month === (int)$d->format('m') && $r->year === (int)$d->format('Y'));
            $chartValues[] = $row ? (int)$row->total : 0;
        }

        // Graphique 2 : répartition des événements par catégorie
        $eventsByCategory = \App\Models\Event::where('user_id', $userId)
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->get();

        $recommendations = $recommender->getRecommendations(auth()->id(), 3);
        $recommendedEvents = collect($recommendations)->map(function($item) {
            $event = \App\Models\Event::find($item['id']);
            if ($event) {
                $event->recommendation_reason = $item['reason'];
                $event->recommendation_score  = $item['score'];
            }
            return $event;
        })->filter();

        return view('organizer.dashboard', [
            'stats' => [
                'total_events'      => $totalEvents,
                'total_participants' => $totalParticipants,
                'pending_events'    => $pendingEvents,
                'published_events'  => $publishedEvents,
            ],
            'recentEvents'      => $recentEvents,
            'chartLabels'       => $chartLabels,
            'chartValues'       => $chartValues,
            'eventsByCategory'  => $eventsByCategory,
            'recommendedEvents' => $recommendedEvents,
        ]);
    })->name('dashboard');

    // CRUD complet sur les événements (sauf show — géré par les routes publiques)
    Route::resource('events', OrganizerEventController::class)->except(['show']);
    Route::get('events/{id}/participants', [OrganizerEventController::class, 'participants'])->name('events.participants');

    Route::get('/participants', function () {
        return view('organizer.participants');
    })->name('participants');

    Route::get('/profile', function () {
        return view('organizer.profile');
    })->name('profile');

    Route::put('/profile', [OrganizerProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [OrganizerProfileController::class, 'updatePassword'])->name('profile.password');
});

// ═══════════════════════════════════════════
// ROUTES ADMINISTRATEUR — Rôle : admin
// ═══════════════════════════════════════════

// Groupe protégé par 'auth' + 'role:admin', préfixe URL /admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Tableau de bord admin : KPIs globaux + graphiques + événements en attente
    Route::get('/dashboard', function (RecommenderService $recommender) {
        $pendingEvents = Event::where('status', 'pending')
            ->with('organiser')
            ->latest()
            ->get();

        $recentRegistrations = Registration::with(['user', 'event'])
            ->latest('registered_at')
            ->take(5)
            ->get();

        // Graphique barres : événements par mois pour l'année en cours (SQLite strftime)
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

        // Graphique camembert : répartition des événements par catégorie
        $categoryData = Event::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();
        $pieLabels = $categoryData->pluck('category')->map(fn ($c) => ucfirst($c))->values()->toArray();
        $pieValues = $categoryData->pluck('total')->values()->toArray();

        $adminRecommendations = $recommender->getRecommendations(auth()->id(), 3);
        $recommendedEvents = collect($adminRecommendations)->map(function($item) {
            $event = \App\Models\Event::find($item['id']);
            if ($event) {
                $event->recommendation_reason = $item['reason'];
                $event->recommendation_score  = $item['score'];
            }
            return $event;
        })->filter();

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
            'recommendedEvents'   => $recommendedEvents,
        ]);
    })->name('dashboard');

    // Gestion des événements : lecture, suppression, approbation, rejet
    Route::resource('events', AdminEventController::class)->except(['create', 'store', 'edit', 'update']);
    Route::post('events/{id}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::post('events/{id}/reject', [AdminEventController::class, 'reject'])->name('events.reject');

    // Gestion des utilisateurs : liste, changement de rôle, activation/désactivation, suppression
    Route::get('/users', function () {
        return view('admin.users.index', [
            'users' => User::orderBy('name')->get(),
        ]);
    })->name('users.index');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.updateRole');
    Route::patch('/users/{user}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Page de statistiques globales : KPIs, graphique ligne des inscriptions, top événements
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

        // Inscriptions par mois pour l'année en cours
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

    // Route::get('/partners', function () {
    //     return view('admin.partners');
    // })->name('partners');
});

// ═══════════════════════════════════════════
// ROUTES PARTICIPANT — Rôle : participant
// ═══════════════════════════════════════════

// Groupe protégé par 'auth' + 'role:participant', préfixe URL /participant
Route::middleware(['auth', 'role:participant'])->prefix('participant')->name('participant.')->group(function () {

    // Tableau de bord participant : événements à venir, historique, recommandations personnalisées
    Route::get('/dashboard', function (RecommenderService $recommender) {
        $user = auth()->user();

        // Inscriptions à venir
        $upcomingRegistrations = $user->registrations()
            ->with('event')
            ->whereHas('event', fn($q) => $q->where('date', '>', now()))
            ->where('status', 'confirmed')
            ->latest()
            ->take(5)
            ->get();

        $history = $user->registrations()
            ->with('event')
            ->latest('registered_at')
            ->get();

        // Recommandations via l'API Python
        $recommendations = $recommender->getRecommendations($user->id, 3);

        // Convertir en objets Event pour la vue
        $recommendedEvents = collect($recommendations)->map(function($item) {
            $event = \App\Models\Event::find($item['id']);
            if ($event) {
                $event->recommendation_reason = $item['reason'];
                $event->recommendation_score  = $item['score'];
            }
            return $event;
        })->filter();

        return view('participant.dashboard', compact('upcomingRegistrations', 'history', 'recommendedEvents'));
    })->name('dashboard');

    Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');

    Route::get('/profile', function () {
        return view('participant.profile');
    })->name('profile');
});

// ═══════════════════════════════════════════
// ROUTES PROFIL — Tous les rôles connectés
// ═══════════════════════════════════════════

// Modification du profil accessible à admin, organisateur et participant
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ═══════════════════════════════════════════
// ROUTES INSCRIPTION — Tout utilisateur connecté
// ═══════════════════════════════════════════

// Actions d'inscription et d'annulation accessibles à tous les rôles authentifiés
Route::middleware('auth')->group(function () {
    Route::post('events/{id}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::delete('registrations/{id}', [RegistrationController::class, 'destroy'])->name('registrations.cancel');
});
