<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// Contrôleur gérant la connexion et la déconnexion des utilisateurs
class AuthenticatedSessionController extends Controller
{
    // Affiche le formulaire de connexion
    public function create(): View
    {
        return view('auth.login');
    }

    // Traite la soumission du formulaire de connexion
    public function store(Request $request): RedirectResponse
    {
        // Validation minimale : email valide et mot de passe présent
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Auth::attempt() vérifie les identifiants contre la base de données
        // Le second paramètre active le cookie "se souvenir de moi" si coché
        // Retourne false si email introuvable ou mot de passe incorrect
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
        }

        // Régénération de l'ID de session après connexion réussie
        // Protection contre les attaques de fixation de session (session fixation attack)
        $request->session()->regenerate();

        $user = $request->user();

        // Vérification du statut du compte : les comptes inactifs sont bloqués à la connexion
        if ($user->status === 'inactive') {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account is deactivated.']);
        }

        // Redirection selon le rôle de l'utilisateur connecté
        // admin → tableau de bord administrateur
        // organisateur → tableau de bord organisateur
        // participant (défaut) → tableau de bord participant
        return redirect()->intended(match ($user->role) {
            'admin'        => route('admin.dashboard'),
            'organisateur' => route('organizer.dashboard'),
            default        => route('participant.dashboard'),
        });
    }

    // Déconnexion : invalide la session et régénère le token CSRF
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
