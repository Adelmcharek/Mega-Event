<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\Partner;

// Contrôleur gérant les événements côté administrateur
// Accès réservé aux utilisateurs avec le rôle 'admin' (middleware 'role:admin')
// L'administrateur peut consulter, approuver, rejeter et supprimer tous les événements
class EventController extends Controller
{
    // Affiche le formulaire de création d'un événement (admin publie directement)
    public function create(): View
    {
        return view('organizer.events.create', [
            'formAction' => route('admin.events.store'),
            'cancelUrl'  => route('admin.events.index'),
        ]);
    }

    // Enregistre un nouvel événement — statut publié directement pour l'admin
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|in:professional,cultural,corporate,educational,social,sports',
            'date'        => 'required|date|after:now',
            'location'    => 'required|string|max:255',
            'lat'         => 'nullable|numeric|between:-90,90',
            'lng'         => 'nullable|numeric|between:-180,180',
            'capacity'    => 'required|integer|min:1',
            'price'       => 'nullable|numeric|min:0',
            'visibility'  => 'required|in:public,private',
            'cover'       => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover_image'] = $request->file('cover')->store('covers', 'public');
        }

        // Si c'est un admin → publié directement, sinon → en attente de validation
        $status = auth()->user()->role === 'admin' ? 'published' : 'pending';

        $validated['status']  = $status;
        $validated['user_id'] = auth()->id();
        $validated['price']   = $validated['price'] ?? 0;
        $validated['lat']     = $request->lat ?: null;
        $validated['lng']     = $request->lng ?: null;

        $event = Event::create($validated);

        if ($request->has('partners')) {
            foreach ($request->partners as $partnerData) {
                if (!empty($partnerData['name'])) {
                    $event->partners()->create([
                        'name'    => $partnerData['name'],
                        'website' => $partnerData['website'] ?? null,
                        'email'   => $partnerData['email'] ?? null,
                        'phone'   => $partnerData['phone'] ?? null,
                    ]);
                }
            }
        }

        if (auth()->user()->role === 'admin') {
            $message = __('app.event_published_success');
        } else {
            $message = __('app.event_submitted_success');
        }

        return redirect()->route('admin.events.index')
            ->with('success', $message);
    }

    // Liste tous les événements avec filtres optionnels (status, organisateur, plage de dates)
    public function index(Request $request): View
    {
        $query = Event::with('organiser')->withCount('registrations')->latest();

        // Filtre par statut (draft, pending, published, rejected)
        if ($request->filled('status')) {
            $query->where('status', strtolower($request->status));
        }

        // Filtre par nom d'organisateur (recherche partielle LIKE)
        if ($request->filled('organizer')) {
            $query->whereHas('organiser', fn ($q) =>
                $q->where('name', 'like', '%' . $request->organizer . '%')
            );
        }

        // Filtre par plage de dates
        if ($request->filled('from')) {
            $query->where('date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('date', '<=', $request->to . ' 23:59:59');
        }

        $events = $query->get();

        return view('admin.events.index', compact('events'));
    }

    // Affiche le détail complet d'un événement avec ses participants et partenaires
    public function show(int $id): View
    {
        $event = Event::with(['organiser', 'registrations.user', 'partners'])->findOrFail($id);

        return view('admin.events.show', compact('event'));
    }

    // Approuve un événement : change le statut de 'pending' à 'published'
    // L'événement devient visible publiquement sur la plateforme
    public function approve(int $id): RedirectResponse
    {
        Event::findOrFail($id)->update(['status' => 'published']);

        return redirect()->route('admin.events.index')
            ->with('success', __('app.success_approved'));
    }

    // Rejette un événement : change le statut à 'rejected' et enregistre le motif
    // Le motif de rejet est optionnel mais recommandé pour informer l'organisateur
    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        Event::findOrFail($id)->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return redirect()->route('admin.events.index')
            ->with('warning', __('app.success_rejected'));
    }

    // Supprime définitivement un événement et son image de couverture
    // Les inscriptions associées sont supprimées en cascade (contrainte FK)
    public function destroy(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);

        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', __('app.success_event_deleted'));
    }
}
