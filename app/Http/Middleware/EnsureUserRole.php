<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== $role) {
            // Redirect to their correct dashboard
            return redirect()->route(match($request->user()->role) {
                'admin'        => 'admin.dashboard',
                'organisateur' => 'organizer.dashboard',
                default        => 'participant.dashboard',
            });
        }

        return $next($request);
    }
}
