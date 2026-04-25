<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

// Contrôleur gérant l'inscription de nouveaux utilisateurs
class RegisteredUserController extends Controller
{
    // Affiche le formulaire d'inscription
    public function create(): View
    {
        return view('auth.register');
    }

    // Traite la soumission du formulaire d'inscription
    public function store(Request $request): RedirectResponse
    {
        // Validation des données du formulaire
        // name : requis, texte, max 255 caractères
        // email : requis, format email valide, unique dans la table users
        // password : requis, confirmé (champ password_confirmation), règles par défaut Laravel
        // role : uniquement 'participant' ou 'organisateur' (admin créé manuellement)
        // avatar : optionnel, image uniquement, formats autorisés, max 2 Mo
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role'     => ['required', 'in:participant,organisateur'],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'Veuillez saisir une adresse email valide (ex : nom@exemple.com).',
            'email.unique'   => 'Cette adresse email est déjà utilisée. Veuillez en choisir une autre.',
            'email.max'      => 'L\'adresse email ne peut pas dépasser 255 caractères.',
        ]);

        // Stockage de l'avatar dans le dossier public/avatars si fourni
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Création de l'utilisateur en base de données
        // Le mot de passe est haché automatiquement via bcrypt grâce au cast 'hashed' du modèle
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'avatar'   => $avatarPath,
        ]);

        // Connexion automatique après inscription
        Auth::login($user);

        // Envoi de l'email de vérification (géré par MustVerifyEmail)
        // L'erreur est capturée pour ne pas bloquer l'inscription si le serveur mail est indisponible
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            \Log::error('Verification email failed: ' . $e->getMessage());
        }

        // Redirection vers la page de notice de vérification email
        // L'utilisateur doit vérifier son email avant d'accéder à son tableau de bord
        return redirect()->route('verification.notice')
            ->with('success', 'Account created! Please check your email to verify your address.');
    }
}
