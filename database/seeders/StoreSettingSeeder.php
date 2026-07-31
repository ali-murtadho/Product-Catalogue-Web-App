<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class StoreSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoreSetting::updateOrCreate(
            ['id' => 1],
            [
                'store_name' => 'Toko Default',
                'wa_numbers' => ['6281234567890'],
                'wa_template' => null,
                'address' => null,
                'social_links' => ['instagram' => '', 'tiktok' => '', 'facebook' => ''],
                'logo_path' => null,
            ]
        );
    }
}
