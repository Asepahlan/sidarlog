<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        
        // Define groups for premium layout
        $permissionGroups = [
            'Dashboard' => ['dashboard.view'],
            'Master Barang' => ['barang.view', 'barang.create', 'barang.edit', 'barang.delete', 'barang.restore', 'barang.force-delete'],
            'Master Data Pendukung' => ['master.kategori', 'master.satuan', 'master.lokasi', 'master.sumber-anggaran', 'master.pihak-kesatu', 'master.pihak-kedua', 'master.bap'],
            'Manajemen Gudang' => ['gudang.view', 'gudang.manage'],
            'Mutasi Barang' => ['mutasi.view', 'mutasi.create', 'mutasi.approve'],
            'Transaksi (Masuk/Keluar)' => ['transaksi.masuk.view', 'transaksi.masuk.create', 'transaksi.keluar.view', 'transaksi.keluar.create', 'transaksi.delete'],
            'Stock Opname' => ['opname.view', 'opname.create'],
            'Laporan' => ['laporan.view', 'laporan.export'],
            'Pengaturan & Sistem' => ['activity-log.view', 'user.view', 'user.manage', 'role.view', 'role.manage', 'settings.manage', 'system.optimize'],
        ];

        return view('pages.sistem.roles', compact('roles', 'permissionGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan dan hak akses diperbarui.');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:roles,name,'.$id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        $role->update(['name' => $request->name]);

        // Don't modify super_admin permissions from web to prevent lockout
        if ($role->name !== 'super_admin') {
            $role->syncPermissions($request->permissions ?? []);
        } else {
            // Force super_admin to have all permissions
            $role->syncPermissions(Permission::all());
        }

        return redirect()->route('roles.index')->with('success', 'Role dan hak akses berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->name === 'super_admin') {
            return redirect()->route('roles.index')->with('error', 'Role Super Admin tidak dapat dihapus.');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
