<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jabatan;
use App\Models\Bidang;
use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'jabatan', 'bidang'])->latest()->get();
        $roles = Role::all();
        $jabatans = Jabatan::all();
        $bidangs = Bidang::all();
        return view('pages.sistem.users', compact('users', 'roles', 'jabatans', 'bidangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:users',
            'nama_lengkap' => 'required',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $user = User::create([
            'nip' => $request->nip,
            'name' => $request->nama_lengkap,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan_id' => $request->jabatan_id,
            'bidang_id' => $request->bidang_id,
            'status_pegawai' => 'aktif'
        ]);

        $user->assignRole($request->role);
        ActivityLog::log("Menambah user baru: {$user->nama_lengkap}", "Manajemen User", $request->except('password'));

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($id)
            ],
            'role' => 'required'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'jabatan_id' => $request->jabatan_id,
            'bidang_id' => $request->bidang_id,
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);
        ActivityLog::log("Memperbarui data user: {$user->nama_lengkap}", "Manajemen User", $request->except('password'));

        return redirect()->back()->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus diri sendiri');
        }
        
        ActivityLog::log("Menghapus user: {$user->nama_lengkap}", "Manajemen User");
        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Reset to default password 'password'
        $user->update([
            'password' => Hash::make('password')
        ]);

        ActivityLog::log("Mereset password user ke default: {$user->nama_lengkap}", "Manajemen User");

        return redirect()->back()->with('success', "Password untuk {$user->nama_lengkap} berhasil direset ke password default ('password')");
    }
}
