<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

// Contrôleur gérant les événements côté organisateur
// Protégé par les middlewares 'auth' et 'role:organisateur' (définis dans les routes)
// Un organisateur ne peut accéder qu'à ses propres événements (filtré par user_id)
class EventController extends Controller
{
    // Liste tous les événements créés par l'organisateur connecté
    // withCount('registrations') charge le nombre d'inscriptions en une seule requête SQL
    public function index(): View
    {
        $events = Event::where('user_id', auth()->id())
            ->withCount('registrations')
            ->latest()
            ->get();

        return view('organizer.events.index', compact('events'));
    }

    // Affiche le formulaire de création d'un nouvel événement
    public function create(): View
    {
        return view('organizer.events.create');
    }

    // Enregistre un nouvel événement en base de données
    public function store(Request $request): RedirectResponse
    {
        // Validation des champs obligatoires et optionnels
        // category : liste fermée de catégories autorisées
        // date : doit être dans le futur (after:now)
        // lat/lng : coordonnées géographiques optionnelles pour la carte
        // cover : image de couverture, max 5 Mo
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

        // Stockage de l'image de couverture dans le dossier public/covers
        if ($request->hasFile('cover')) {
            $validated['cover_image'] = $request->file('cover')->store('covers', 'public');
        }

        // Si c'est un admin → publié directement, sinon → en attente de validation
        $validated['status']  = auth()->user()->role === 'admin' ? 'published' : 'pending';
        $validated['user_id'] = auth()->id();
        $validated['price']   = $validated['price'] ?? 0;
        $validated['lat']     = $request->lat ?: null;
        $validated['lng']     = $request->lng ?: null;

        $event = Event::create($validated);

        // Création des partenaires associés à l'événement en boucle
        // Seuls les partenaires avec un nom non vide sont enregistrés
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

        return redirect()->route('organizer.events.index')
            ->with('success', $message);
    }

    // Affiche le formulaire d'édition — vérifie que l'événement appartient bien à l'organisateur
    public function edit(int $id): View
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);

        return view('organizer.events.edit', compact('event'));
    }

    // Met à jour un événement existant
    public function update(Request $request, int $id): RedirectResponse
    {
        // Sécurité : findOrFail avec filtre user_id empêche l'accès aux événements d'autres organisateurs
        $event = Event::where('user_id', auth()->id())->findOrFail($id);

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

        // Remplacement de l'ancienne image si une nouvelle est fournie
        // L'ancienne image est supprimée du disque pour éviter les fichiers orphelins
        if ($request->hasFile('cover')) {
            if ($event->cover_image) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $validated['cover_image'] = $request->file('cover')->store('covers', 'public');
        }

        $validated['lat'] = $request->lat ?: null;
        $validated['lng'] = $request->lng ?: null;

        $event->update($validated);

        // Mise à jour des partenaires : suppression de tous les anciens, puis recréation
        // Approche simple qui évite de gérer les mises à jour partielles
        $event->partners()->delete();
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

        return redirect()->route('organizer.events.index')
            ->with('success', __('app.success_event_updated'));
    }

    // Supprime un événement et son image de couverture associée
    public function destroy(int $id): RedirectResponse
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);

        // Suppression du fichier image du disque avant suppression de l'enregistrement
        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }

        $event->delete();

        return redirect()->route('organizer.events.index')
            ->with('success', __('app.success_event_deleted'));
    }

    // Affiche la liste des participants inscrits à un événement
    // Calcule les statistiques de remplissage en temps réel
    public function participants(int $id): View
    {
        $event         = Event::where('user_id', auth()->id())->findOrFail($id);
        $registrations = Registration::where('event_id', $id)
            ->where('status', '!=', 'cancelled')
            ->with('user')
            ->get();

        $totalRegistered = $registrations->count();
        $spotsLeft       = max(0, $event->capacity - $totalRegistered);
        $fillRate        = $event->capacity > 0
            ? round(($totalRegistered / $event->capacity) * 100, 1)
            : 0;

        return view('organizer.events.participants', [
            'event'        => $event,
            'eventName'    => $event->title,
            'participants' => $registrations,
            'stats'        => [
                'total'      => $totalRegistered,
                'spots_left' => $spotsLeft,
                'attendance' => $fillRate,
            ],
        ]);
    }
}
