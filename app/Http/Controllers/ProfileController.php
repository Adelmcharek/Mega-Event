<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

// Contrôleur gérant le profil utilisateur — accessible à tous les rôles connectés
class ProfileController extends Controller
{
    // Affiche le formulaire d'édition du profil
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    // Met à jour les informations de profil (nom, email, avatar)
    public function update(Request $request)
    {
        $user = auth()->user();

        // email unique sauf pour l'utilisateur courant (ignore,$user->id)
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // Remplacement de l'avatar : suppression de l'ancien fichier avant stockage du nouveau
        // pour éviter l'accumulation de fichiers orphelins sur le disque
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', __('app.success_profile_updated'));
    }

    // Change le mot de passe après vérification de l'ancien mot de passe
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        // Vérification que l'ancien mot de passe correspond bien au hash stocké en base
        // Empêche un changement de mot de passe si la session est compromise
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Nouveau mot de passe haché via bcrypt avant stockage
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', __('app.success_password'));
    }

    // Supprime définitivement le compte utilisateur
    // La suppression en cascade (définie en migration) efface toutes les données liées
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = auth()->user();

        // Confirmation du mot de passe obligatoire avant suppression — action irréversible
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        // Suppression de l'avatar du disque avant suppression du compte
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Déconnexion et invalidation de la session avant suppression
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('home')
            ->with('success', __('app.success_account_deleted'));
    }
}
