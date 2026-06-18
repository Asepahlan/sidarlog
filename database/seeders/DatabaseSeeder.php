<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jabatan;
use App\Models\Bidang;
use App\Models\Instansi;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles
        $roles = [
            'super_admin',
            'admin_logistik',
            'staff_gudang',
            'kabid',
            'pimpinan',
            'operator_portal'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Create Organization Master Data
        $jabatan = Jabatan::create(['nama_jabatan' => 'Kepala Bidang']);
        $bidang = Bidang::create(['nama_bidang' => 'Logistik']);
        $instansi = Instansi::create([
            'nama_instansi' => 'Dinas Komunikasi dan Informatika',
            'kode_instansi' => 'DISKOMINFO'
        ]);

        // 3. Create Inventory Master Data
        Warehouse::create(['kode_gudang' => 'GD-UTAMA', 'nama_gudang' => 'Gudang Utama', 'lokasi' => 'Lantai 1']);
        Category::create(['nama_kategori' => 'Alat Tulis Kantor']);
        Unit::create(['nama_satuan' => 'Pcs', 'simbol' => 'pcs']);

        // 4. Create Super Admin User
        $admin = User::create([
            'nip' => '1234567890',
            'name' => 'Super Admin',
            'nama_lengkap' => 'Administrator Utama',
            'email' => 'admin@sidarlog.test',
            'password' => Hash::make('password'),
            'jabatan_id' => $jabatan->id,
            'bidang_id' => $bidang->id,
            'instansi_opd' => $instansi->nama_instansi,
            'status_pegawai' => 'Aktif',
        ]);

        $admin->assignRole('super_admin');

        // 5. Dummy data for testing notifications & UI
        $this->call(SampleDataSeeder::class);
        $this->call(DummyNotificationSeeder::class);

        echo "Seeding completed. Login NIP: 1234567890, Pass: password\n";
    }
}
