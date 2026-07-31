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
        // Setup store settings once before all iterations
        StoreSetting::query()->delete();
        StoreSetting::create([
            'store_name' => 'Toko Test',
            'wa_numbers' => ['6281234567890', '6289876543210'],
            'wa_template' => null,
            'address' => 'Jl. Test No. 1',
            'social_links' => null,
            'logo_path' => null,
        ]);

        $this
            ->limitTo(5)
            ->minimumEvaluationRatio(0.5)
            ->forAll(
                $this->cartGenerator(),
                $this->buyerGenerator()
            )
            ->then(function (array $cart, array $buyer) {
                // Set the cart in session before testing
                session(['cart' => $cart]);

                // Verify cart is in session before sending
                $this->assertNotEmpty(session('cart'), 'Cart harus terisi sebelum pengiriman');

                // Test sendToWhatsApp using Livewire
                Livewire::test(CartManager::class)
                    ->set('name', $buyer['name'])
                    ->set('phone', $buyer['phone'])
                    ->set('address', $buyer['address'])
                    ->set('notes', $buyer['notes'])
                    ->call('sendToWhatsApp');

                // Property assertion: session cart must be empty after successful send
                $this->assertTrue(
                    session('cart') === null || session('cart') === [],
                    'Session cart harus bernilai null atau empty array setelah pengiriman'
                );

                // Force garbage collection to prevent memory exhaustion
                gc_collect_cycles();
            });
    }

    /**
     * Generate a cart with 1-2 items matching session cart structure.
     * Simplified to reduce memory usage.
     */
    private function cartGenerator(): Generator
    {
        return Generator\map(
            function (array $data) {
                $count = $data[0];
                return array_slice([$data[1], $data[2]], 0, $count);
            },
            Generator\tuple(
                Generator\choose(1, 2),
                $this->cartItemGenerator(),
                $this->cartItemGenerator()
            )
        );
    }

    /**
     * Generate a single cart item matching the session cart structure.
     */
    private function cartItemGenerator(): Generator
    {
        return Generator\map(
            function (array $data) {
                [$nameIndex, $qty, $price] = $data;

                $names = [
                    'Kemeja Flanel',
                    'Kaos Polos',
                    'Celana Jeans',
                    'Jaket Hoodie',
                    'Tas Ransel',
                ];

                return [
                    'product_id' => $nameIndex + 1,
                    'variant_id' => null,
                    'name' => $names[$nameIndex % count($names)],
                    'variant' => null,
                    'price' => $price,
                    'qty' => $qty,
                    'max_stock' => 100,
                    'image' => 'products/sample.jpg',
                ];
            },
            Generator\tuple(
                Generator\choose(0, 4),         // name index
                Generator\choose(1, 5),         // qty (1-5)
                Generator\choose(10000, 200000) // price
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
                [$nameIndex, $phoneSuffix, $addressIndex] = $data;

                $names = [
                    'Ahmad Rizki',
                    'Siti Nurhaliza',
                    'Budi Santoso',
                    'Dewi Lestari',
                ];

                $addresses = [
                    'Jl. Merdeka No. 123, Jakarta Pusat',
                    'Jl. Sudirman No. 45, Bandung',
                    'Jl. Diponegoro No. 67, Surabaya',
                ];

                return [
                    'name' => $names[$nameIndex % count($names)],
                    'phone' => '628' . str_pad((string) abs($phoneSuffix), 10, '0', STR_PAD_LEFT),
                    'address' => $addresses[$addressIndex % count($addresses)],
                    'notes' => 'Test notes',
                ];
            },
            Generator\tuple(
                Generator\choose(0, 3),                   // name index
                Generator\choose(1000000000, 9999999999), // phone suffix
                Generator\choose(0, 2)                    // address index
            )
        );
    }
}
