<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jabatan;
use App\Models\Bidang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $jabatan = Jabatan::first();
        $bidang = Bidang::first();

        $users = [
            [
                'nip' => '1111111111',
                'name' => 'Logistik Admin',
                'nama_lengkap' => 'Andi Logistikawan',
                'email' => 'andi@sidarlog.test',
                'password' => Hash::make('password'),
                'role' => 'admin_logistik',
            ],
            [
                'nip' => '2222222222',
                'name' => 'Staff Gudang',
                'nama_lengkap' => 'Budi Gudang',
                'email' => 'budi@sidarlog.test',
                'password' => Hash::make('password'),
                'role' => 'staff_gudang',
            ],
            [
                'nip' => '3333333333',
                'name' => 'Kabid Logistik',
                'nama_lengkap' => 'Citra Kabid',
                'email' => 'citra@sidarlog.test',
                'password' => Hash::make('password'),
                'role' => 'kabid',
            ],
            [
                'nip' => '4444444444',
                'name' => 'Pimpinan',
                'nama_lengkap' => 'Dedi Pimpinan',
                'email' => 'dedi@sidarlog.test',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
            ],
            [
                'nip' => '5555555555',
                'name' => 'Operator Portal',
                'nama_lengkap' => 'Eka Operator',
                'email' => 'eka@sidarlog.test',
                'password' => Hash::make('password'),
                'role' => 'operator_portal',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $userData['jabatan_id'] = $jabatan->id;
            $userData['bidang_id'] = $bidang->id;
            $userData['instansi_opd'] = 'Dinas Komunikasi dan Informatika';
            $userData['status_pegawai'] = 'Aktif';

            $user = User::updateOrCreate(['nip' => $userData['nip']], $userData);
            $user->assignRole($role);
        }
    }
}
