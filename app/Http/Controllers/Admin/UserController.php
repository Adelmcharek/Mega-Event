<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,organisateur,participant']);
        $user->update(['role' => $request->role]);
        return back()->with('success', __('app.success_role_updated'));
    }

    public function toggle(User $user)
    {
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);
        return back()->with('success', __('app.active'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account from here.');
        }

        if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
            \Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return back()->with('success', __('app.success_user_deleted'));
    }
}
