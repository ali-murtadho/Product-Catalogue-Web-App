<?php

namespace Tests\Property;

use App\Livewire\CartManager;
use App\Models\StoreSetting;
use Eris\TestTrait;
use Eris\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartSessionResetPropertyTest extends TestCase
{
    use TestTrait;
    use RefreshDatabase;

    /**
     * Feature: product-catalogue-whatsapp, Property 10: Session keranjang kosong setelah pengiriman pesanan
     * Validates: Requirements 5.4
     *
     * For any pengiriman pesanan yang berhasil ke WhatsApp, session keranjang
     * SHALL bernilai kosong (empty array) setelah operasi selesai.
     */
    public function testCartSessionIsEmptyAfterSuccessfulOrder(): void
    {
        $this
            ->limitTo(100)
            ->minimumEvaluationRatio(0.5)
            ->forAll(
                $this->cartGenerator(),
                $this->buyerGenerator(),
                $this->waNumbersGenerator()
            )
            ->withMaxSize(50)
            ->then(function (array $cart, array $buyer, array $waNumbers) {
                // Setup store settings in database
                StoreSetting::query()->delete();
                StoreSetting::create([
                    'store_name' => 'Toko Test',
                    'wa_numbers' => $waNumbers,
                    'wa_template' => null,
                    'address' => 'Jl. Test No. 1',
                    'social_links' => null,
                    'logo_path' => null,
                ]);

                // Set the cart in session before testing
                session(['cart' => $cart]);

                // Verify cart is in session before sending
                $this->assertNotEmpty(session('cart'), 'Cart harus terisi sebelum pengiriman');

                // Test sendToWhatsApp using Livewire
                $component = Livewire::test(CartManager::class)
                    ->set('name', $buyer['name'])
                    ->set('phone', $buyer['phone'])
                    ->set('address', $buyer['address'])
                    ->set('notes', $buyer['notes'])
                    ->call('sendToWhatsApp');

                // Property assertion: session cart must be empty after successful send
                $this->assertEmpty(
                    session('cart'),
                    'Session cart harus kosong (empty) setelah pengiriman pesanan berhasil'
                );

                // Also verify that session('cart') is actually forgotten (null) or empty
                $this->assertTrue(
                    session('cart') === null || session('cart') === [],
                    'Session cart harus bernilai null atau empty array setelah pengiriman'
                );
            });
    }

    /**
     * Generate an array of 1-3 valid WA numbers starting with "62".
     */
    private function waNumbersGenerator(): Generator
    {
        return Generator\bind(
            Generator\choose(1, 3),
            function (int $count) {
                $generators = [];
                for ($i = 0; $i < $count; $i++) {
                    $generators[] = Generator\map(
                        function (int $suffix) {
                            return '62' . str_pad((string) abs($suffix), 10, '0', STR_PAD_LEFT);
                        },
                        Generator\choose(1000000000, 9999999999)
                    );
                }
                return Generator\tuple(...$generators);
            }
        );
    }

    /**
     * Generate a cart with 1-5 items matching session cart structure.
     */
    private function cartGenerator(): Generator
    {
        return Generator\bind(
            Generator\choose(1, 5),
            function (int $count) {
                $generators = [];
                for ($i = 0; $i < $count; $i++) {
                    $generators[] = $this->cartItemGenerator();
                }
                return Generator\tuple(...$generators);
            }
        );
    }

    /**
     * Generate a single cart item matching the session cart structure.
     */
    private function cartItemGenerator(): Generator
    {
        return Generator\map(
            function (array $data) {
                [$nameIndex, $variantIndex, $qty, $price] = $data;

                $names = [
                    'Kemeja Flanel',
                    'Kaos Polos',
                    'Celana Jeans',
                    'Jaket Hoodie',
                    'Tas Ransel',
                    'Sepatu Sneakers',
                    'Topi Baseball',
                    'Dompet Kulit',
                    'Kacamata Sport',
                    'Jam Tangan',
                ];

                $variants = [
                    null,
                    'Ukuran S',
                    'Ukuran M',
                    'Ukuran L',
                    'Ukuran XL',
                    'Warna Hitam',
                    'Warna Putih',
                    'Warna Merah',
                ];

                return [
                    'product_id' => $nameIndex + 1,
                    'variant_id' => $variantIndex > 0 ? $variantIndex : null,
                    'name' => $names[$nameIndex % count($names)],
                    'variant' => $variants[$variantIndex % count($variants)],
                    'price' => $price,
                    'qty' => $qty,
                    'max_stock' => 100,
                    'image' => 'products/sample.jpg',
                ];
            },
            Generator\tuple(
                Generator\choose(0, 9),         // name index
                Generator\choose(0, 7),         // variant index
                Generator\choose(1, 10),        // qty (1-10)
                Generator\choose(10000, 500000) // price (Rp 10.000 - Rp 500.000)
            )
        );
    }

    /**
     * Generate valid buyer data that passes CartManager validation.
     * name >= 3 chars, phone >= 10 chars, address >= 10 chars
     */
    private function buyerGenerator(): Generator
    {
        return Generator\map(
            function (array $data) {
                [$nameIndex, $phoneSuffix, $addressIndex, $noteIndex] = $data;

                $names = [
                    'Ahmad Rizki',
                    'Siti Nurhaliza',
                    'Budi Santoso',
                    'Dewi Lestari',
                    'Andi Pratama',
                    'Rina Wati',
                    'Fajar Hidayat',
                    'Maya Sari',
                ];

                $addresses = [
                    'Jl. Merdeka No. 123, Jakarta Pusat',
                    'Jl. Sudirman No. 45, Bandung',
                    'Jl. Diponegoro No. 67, Surabaya',
                    'Jl. Ahmad Yani No. 89, Semarang',
                    'Jl. Gajah Mada No. 12, Yogyakarta',
                ];

                $notes = [
                    'Kirim sore ya',
                    'Tolong packing bubble wrap',
                    'Jangan kirim hari Minggu',
                    'Titip di satpam',
                    '',
                ];

                return [
                    'name' => $names[$nameIndex % count($names)],
                    'phone' => '628' . str_pad((string) abs($phoneSuffix), 10, '0', STR_PAD_LEFT),
                    'address' => $addresses[$addressIndex % count($addresses)],
                    'notes' => $notes[$noteIndex % count($notes)],
                ];
            },
            Generator\tuple(
                Generator\choose(0, 7),                   // name index
                Generator\choose(1000000000, 9999999999), // phone suffix
                Generator\choose(0, 4),                   // address index
                Generator\choose(0, 4)                    // notes index
            )
        );
    }
}
