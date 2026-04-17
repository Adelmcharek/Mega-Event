<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();

        // ✅ Fixed: use 'status' column instead of 'is_active'
        if ($user->status === 'inactive') {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account is deactivated.']);
        }

        // ✅ Fixed: 'organisateur' instead of 'organizer'
        return redirect()->intended(match ($user->role) {
            'admin'        => route('admin.dashboard'),
            'organisateur' => route('organizer.dashboard'),
            default        => route('participant.dashboard'),
        });
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
