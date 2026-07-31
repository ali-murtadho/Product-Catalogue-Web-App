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
                'store_name' => 'Toko Serba Ada',
                'wa_numbers' => ['6281234567890', '6289876543210'],
                'wa_template' => "Halo, saya ingin memesan:\n\n{items}\n\nTotal: {total}\n\nTerima kasih!",
                'address' => 'Jl. Merdeka No. 123, Kelurahan Sukamaju, Kecamatan Cimanggis, Depok, Jawa Barat 16451',
                'social_links' => [
                    'instagram' => 'https://instagram.com/tokoserbaada',
                    'tiktok' => 'https://tiktok.com/@tokoserbaada',
                    'facebook' => 'https://facebook.com/tokoserbaada',
                ],
                'logo_path' => null,
            ]
        );
    }
}
