<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app_name',
                'value' => 'SIDARLOG',
                'group' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'app_description',
                'value' => 'Sistem Manajemen Logistik & Inventory Modern',
                'group' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'group' => 'general',
                'type' => 'image',
            ],
            [
                'key' => 'app_favicon',
                'value' => null,
                'group' => 'general',
                'type' => 'image',
            ],
            [
                'key' => 'footer_text',
                'value' => '© 2026 Sistem Manajemen Logistik & Inventory Modern (SIDARLOG). All rights reserved.',
                'group' => 'general',
                'type' => 'text',
            ],
            [
                'key' => 'contact_email',
                'value' => 'admin@sidarlog.com',
                'group' => 'contact',
                'type' => 'text',
            ],
            [
                'key' => 'contact_phone',
                'value' => '0265-123456',
                'group' => 'contact',
                'type' => 'text',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
