<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('pages.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'nip' => 'nullable|string|max:20',
        ]);

        $user->update($request->only(['nama_lengkap', 'username', 'email', 'nip']));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    public function settings()
    {
        $settings = \App\Models\Setting::all()->groupBy('group');
        return view('pages.settings', compact('settings'));
    }

    public function updateSystemSettings(Request $request)
    {
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Akses ditolak.');
        }

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            // Handle file uploads for logo/favicon
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('settings', 'public');
                \App\Models\Setting::set($key, '/storage/' . $path);
            } else {
                \App\Models\Setting::set($key, $value);
            }
        }

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
