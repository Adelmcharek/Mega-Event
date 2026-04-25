<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Intergiciel personnalisé pour vérifier le rôle de l'utilisateur
// Protège les routes selon le rôle requis (admin, organisateur, participant)
// Redirige vers le tableau de bord approprié si le rôle ne correspond pas
// Utilisé dans les routes sous forme de middleware 'role:organisateur', 'role:admin', etc.
class EnsureUserRole
{
    // $role : le rôle attendu pour accéder à la route (passé comme paramètre de middleware)
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Si l'utilisateur n'est pas connecté, redirection vers la page de connexion
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Si le rôle de l'utilisateur ne correspond pas au rôle requis par la route,
        // redirection vers son propre tableau de bord (évite l'accès croisé entre rôles)
        if ($request->user()->role !== $role) {
            // Redirection vers le tableau de bord correspondant au rôle réel de l'utilisateur
            return redirect()->route(match($request->user()->role) {
                'admin'        => 'admin.dashboard',
                'organisateur' => 'organizer.dashboard',
                default        => 'participant.dashboard',
            });
        }

        // Rôle valide : on laisse passer la requête vers le contrôleur
        return $next($request);
    }
}
