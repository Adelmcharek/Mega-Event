<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

// Contrôleur gérant les événements côté administrateur
// Accès réservé aux utilisateurs avec le rôle 'admin' (middleware 'role:admin')
// L'administrateur peut consulter, approuver, rejeter et supprimer tous les événements
class EventController extends Controller
{
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
