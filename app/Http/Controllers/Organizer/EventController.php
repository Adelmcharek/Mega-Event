<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::where('user_id', auth()->id())
            ->withCount('registrations')
            ->latest()
            ->get();

        return view('organizer.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('organizer.events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|in:professional,cultural,corporate',
            'date'        => 'required|date|after:now',
            'location'    => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1',
            'price'       => 'nullable|numeric|min:0',
            'visibility'  => 'required|in:public,private',
            'cover'       => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover_image'] = $request->file('cover')->store('covers', 'public');
        }

        $validated['status']  = 'pending';
        $validated['user_id'] = auth()->id();
        $validated['price']   = $validated['price'] ?? 0;

        Event::create($validated);

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event submitted for review.');
    }

    public function edit(int $id): View
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);

        return view('organizer.events.edit', compact('event'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|in:professional,cultural,corporate',
            'date'        => 'required|date|after:now',
            'location'    => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1',
            'price'       => 'nullable|numeric|min:0',
            'visibility'  => 'required|in:public,private',
            'cover'       => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('cover')) {
            if ($event->cover_image) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $validated['cover_image'] = $request->file('cover')->store('covers', 'public');
        }

        $event->update($validated);

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);

        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }

        $event->delete();

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event deleted.');
    }

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
