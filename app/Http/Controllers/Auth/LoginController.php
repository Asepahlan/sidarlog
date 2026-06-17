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
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $user = \App\Models\User::withTrashed()->where('nip', $request->nip)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'nip' => ['NIP tidak terdaftar dalam sistem.'],
            ]);
        }

        if ($user->trashed()) {
            throw ValidationException::withMessages([
                'nip' => ['Akun Anda telah dinonaktifkan.'],
            ]);
        }

        if (!Auth::attempt(['nip' => $request->nip, 'password' => $request->password], $request->remember)) {
            throw ValidationException::withMessages([
                'password' => ['Kata sandi yang Anda masukkan salah.'],
            ]);
        }

        $request->session()->regenerate();
        
        // Log last login
        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
