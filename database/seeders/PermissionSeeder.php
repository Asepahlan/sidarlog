<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── DEFINE ALL PERMISSIONS ──────────────────────────────────────
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Barang
            'barang.view',
            'barang.create',
            'barang.edit',
            'barang.delete',
            'barang.restore',
            'barang.force-delete',

            // Master Data
            'master.kategori',
            'master.satuan',
            'master.lokasi',
            'master.sumber-anggaran',
            'master.jenis-barang',
            'master.klasifikasi-barang',
            'master.pihak-kesatu',
            'master.pihak-kedua',
            'master.bap',

            // Gudang
            'gudang.view',
            'gudang.manage',

            // Mutasi
            'mutasi.view',
            'mutasi.create',
            'mutasi.approve',

            // Transaksi
            'transaksi.masuk.view',
            'transaksi.masuk.create',
            'transaksi.keluar.view',
            'transaksi.keluar.create',
            'transaksi.delete',

            // Stock Opname
            'opname.view',
            'opname.create',

            // Laporan
            'laporan.view',
            'laporan.export',

            // Sistem
            'activity-log.view',
            'user.view',
            'user.manage',
            'role.view',
            'role.manage',
            'settings.manage',
            'system.optimize',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        echo "  ✓ " . count($permissions) . " permissions created/verified\n";

        // ── ASSIGN PERMISSIONS TO ROLES ─────────────────────────────────

        // SUPER ADMIN — gets all permissions (also bypassed via Gate::before)
        $superAdmin = Role::findByName('super_admin', 'web');
        $superAdmin->syncPermissions($permissions);
        echo "  ✓ super_admin → ALL permissions (" . count($permissions) . ")\n";

        // ADMIN LOGISTIK
        $adminLogistik = Role::findByName('admin_logistik', 'web');
        $adminLogistik->syncPermissions([
            'dashboard.view',
            // Barang full
            'barang.view', 'barang.create', 'barang.edit', 'barang.delete', 'barang.restore',
            // Master data full
            'master.kategori', 'master.satuan', 'master.lokasi', 'master.sumber-anggaran',
            'master.jenis-barang', 'master.klasifikasi-barang',
            'master.pihak-kesatu', 'master.pihak-kedua', 'master.bap',
            // Gudang full
            'gudang.view', 'gudang.manage',
            // Mutasi full
            'mutasi.view', 'mutasi.create', 'mutasi.approve',
            // Transaksi full
            'transaksi.masuk.view', 'transaksi.masuk.create',
            'transaksi.keluar.view', 'transaksi.keluar.create',
            'transaksi.delete',
            // Opname full
            'opname.view', 'opname.create',
            // Laporan full
            'laporan.view', 'laporan.export',
            // Log
            'activity-log.view',
        ]);
        echo "  ✓ admin_logistik → 28 permissions\n";

        // STAFF GUDANG
        $staffGudang = Role::findByName('staff_gudang', 'web');
        $staffGudang->syncPermissions([
            'dashboard.view',
            'barang.view',
            'gudang.view',
            'transaksi.masuk.view', 'transaksi.masuk.create',
            'transaksi.keluar.view', 'transaksi.keluar.create',
            'mutasi.view', 'mutasi.create',
            'opname.view', 'opname.create',
        ]);
        echo "  ✓ staff_gudang → 11 permissions\n";

        // KABID
        $kabid = Role::findByName('kabid', 'web');
        $kabid->syncPermissions([
            'dashboard.view',
            'barang.view',
            'gudang.view',
            'mutasi.view', 'mutasi.approve',
            'opname.view',
            'laporan.view', 'laporan.export',
        ]);
        echo "  ✓ kabid → 8 permissions\n";

        // PIMPINAN
        $pimpinan = Role::findByName('pimpinan', 'web');
        $pimpinan->syncPermissions([
            'dashboard.view',
            'barang.view',
            'laporan.view', 'laporan.export',
        ]);
        echo "  ✓ pimpinan → 4 permissions\n";

        // OPERATOR PORTAL (viewer only)
        $operatorPortal = Role::findByName('operator_portal', 'web');
        $operatorPortal->syncPermissions([
            'dashboard.view',
            'barang.view',
        ]);
        echo "  ✓ operator_portal → 2 permissions\n";

        echo "\n  ══ RBAC Seeding Complete ══\n";
    }
}
