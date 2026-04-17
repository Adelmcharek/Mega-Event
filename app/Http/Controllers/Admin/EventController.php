<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::with('organiser')->withCount('registrations')->latest();

        if ($request->filled('status')) {
            $query->where('status', strtolower($request->status));
        }

        if ($request->filled('organizer')) {
            $query->whereHas('organiser', fn ($q) =>
                $q->where('name', 'like', '%' . $request->organizer . '%')
            );
        }

        if ($request->filled('from')) {
            $query->where('date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('date', '<=', $request->to . ' 23:59:59');
        }

        $events = $query->get();

        return view('admin.events.index', compact('events'));
    }

    public function show(int $id): View
    {
        $event = Event::with(['organiser', 'registrations.user', 'partners'])->findOrFail($id);

        return view('admin.events.show', compact('event'));
    }

    public function approve(int $id): RedirectResponse
    {
        Event::findOrFail($id)->update(['status' => 'published']);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event approved and published.');
    }

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
            ->with('warning', 'Event rejected.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);

        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted.');
    }
}
