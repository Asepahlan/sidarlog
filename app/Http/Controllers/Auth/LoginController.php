<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['nip' => $request->nip, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();
            
            // Log last login
            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'nip' => [__('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
