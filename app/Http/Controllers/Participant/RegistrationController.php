<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

// Contrôleur gérant les inscriptions aux événements côté participant
class RegistrationController extends Controller
{
    // Affiche l'historique des inscriptions du participant connecté
    public function index(): View
    {
        $registrations = Registration::where('user_id', auth()->id())
            ->with('event')
            ->latest('registered_at')
            ->get();

        return view('participant.registrations.index', compact('registrations'));
    }

    // Inscrit le participant connecté à un événement
    // Double vérification pour protéger l'intégrité des données
    public function store(int $eventId): RedirectResponse
    {
        $event = Event::findOrFail($eventId);

        // Vérification 1 : l'événement doit être publié pour accepter des inscriptions
        if ($event->status !== 'published') {
            return back()->with('error', 'This event is not available for registration.');
        }

        // Vérification 2 (anti-doublon) : l'utilisateur ne doit pas être déjà inscrit
        // Complète la contrainte UNIQUE (user_id, event_id) de la base de données
        // pour éviter une erreur SQL et afficher un message convivial à la place
        $alreadyRegistered = Registration::where('user_id', auth()->id())
            ->where('event_id', $eventId)
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', __('app.error_already_registered'));
        }

        // Vérification 3 (capacité) : compte uniquement les inscriptions non annulées
        // pour permettre aux nouvelles inscriptions de prendre les places libérées
        $filledSpots = Registration::where('event_id', $eventId)
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($filledSpots >= $event->capacity) {
            return back()->with('error', __('app.error_event_full'));
        }

        // Création de l'inscription avec statut 'confirmed' directement
        Registration::create([
            'user_id'       => auth()->id(),
            'event_id'      => $eventId,
            'status'        => 'confirmed',
            'registered_at' => now(),
        ]);

        return back()->with('success', __('app.success_registered'));
    }

    // Annule une inscription — ne supprime pas l'enregistrement, change le statut à 'cancelled'
    // Impossible d'annuler une inscription pour un événement déjà passé
    public function cancel(int $id): RedirectResponse
    {
        // Sécurité : filtre par user_id pour empêcher l'annulation des inscriptions d'autres utilisateurs
        $registration = Registration::where('user_id', auth()->id())
            ->with('event')
            ->findOrFail($id);

        // Vérification de la date : les événements passés ne peuvent plus être annulés
        if ($registration->event->date->isPast()) {
            return back()->with('error', 'Cannot cancel registration for past events.');
        }

        $registration->update(['status' => 'cancelled']);

        return back()->with('success', __('app.success_cancelled'));
    }
}
