<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('phone', $credentials['phone'])->first();

        // One generic message for every credential failure so the form
        // can't be used to enumerate which phone numbers are admin accounts.
        $isAdminAccount = $user && in_array($user->role, ['admin', 'employee']);

        if (! $isAdminAccount
            || ! Auth::attempt(['phone' => $credentials['phone'], 'password' => $credentials['password']])) {
            return back()->withErrors(['phone' => 'Invalid phone number or password.'])->withInput();
        }

        if (! $user->isActive()) {
            Auth::logout();
            return back()->withErrors(['phone' => 'Your account has been suspended.'])->withInput();
        }

        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}