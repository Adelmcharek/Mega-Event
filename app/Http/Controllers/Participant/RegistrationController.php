<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(): View
    {
        $registrations = Registration::where('user_id', auth()->id())
            ->with('event')
            ->latest('registered_at')
            ->get();

        return view('participant.registrations.index', compact('registrations'));
    }

    public function store(int $eventId): RedirectResponse
    {
        $event = Event::findOrFail($eventId);

        if ($event->status !== 'published') {
            return back()->with('error', 'This event is not available for registration.');
        }

        $alreadyRegistered = Registration::where('user_id', auth()->id())
            ->where('event_id', $eventId)
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'You are already registered for this event.');
        }

        $filledSpots = Registration::where('event_id', $eventId)
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($filledSpots >= $event->capacity) {
            return back()->with('error', 'This event is full.');
        }

        Registration::create([
            'user_id'       => auth()->id(),
            'event_id'      => $eventId,
            'status'        => 'confirmed',
            'registered_at' => now(),
        ]);

        return back()->with('success', 'You have successfully registered for this event!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $registration = Registration::where('user_id', auth()->id())
            ->with('event')
            ->findOrFail($id);

        if ($registration->event->date->isPast()) {
            return back()->with('error', 'Cannot cancel registration for past events.');
        }

        $registration->update(['status' => 'cancelled']);

        return back()->with('success', 'Registration cancelled successfully.');
    }
}
