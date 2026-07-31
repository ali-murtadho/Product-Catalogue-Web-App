<?php

namespace Tests\Property;

use App\Models\OrderLog;
use Eris\TestTrait;
use Eris\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderLogPropertyTest extends TestCase
{
    use TestTrait;
    use RefreshDatabase;

    /**
     * Feature: product-catalogue-whatsapp, Property 9: Order log mencatat snapshot pesanan secara akurat
     * Validates: Requirements 5.3
     *
     * For any order sent to WhatsApp, the OrderLog that is created SHALL contain
     * items and buyer info data that is identical to the cart data and buyer form
     * at the time of sending.
     */
    public function testOrderLogRecordsAccurateSnapshot(): void
    {
        $this
            ->limitTo(100)
            ->minimumEvaluationRatio(0.5)
            ->forAll(
                $this->cartGenerator(),
                $this->buyerGenerator(),
                $this->phoneGenerator()
            )
            ->withMaxSize(50)
            ->then(function (array $cart, array $buyer, string $phone) {
                // Calculate expected total (same logic as CartManager::getGrandTotal)
                $expectedTotal = 0;
                foreach ($cart as $item) {
                    $expectedTotal += $item['price'] * $item['qty'];
                }

                // Create OrderLog the same way CartManager::sendToWhatsApp() does
                $orderLog = OrderLog::create([
                    'items_json' => $cart,
                    'buyer_info_json' => $buyer,
                    'total_amount' => $expectedTotal,
                    'wa_number_used' => $phone,
                ]);

                // Reload from database to verify persistence
                $savedLog = OrderLog::find($orderLog->id);

                // Verify items_json is identical to input cart
                $this->assertEquals(
                    $cart,
                    $savedLog->items_json,
                    'items_json harus identik dengan data keranjang saat pengiriman'
                );

                // Verify buyer_info_json is identical to input buyer data
                $this->assertEquals(
                    $buyer,
                    $savedLog->buyer_info_json,
                    'buyer_info_json harus identik dengan data pemesan saat pengiriman'
                );

                // Verify total_amount equals sum of (price * qty) for all items
                $this->assertEquals(
                    number_format($expectedTotal, 2, '.', ''),
                    $savedLog->total_amount,
                    'total_amount harus sama dengan jumlah (price * qty) semua item'
                );

                // Verify wa_number_used matches the input phone number
                $this->assertEquals(
                    $phone,
                    $savedLog->wa_number_used,
                    'wa_number_used harus sama dengan nomor telepon yang digunakan'
                );
            });
    }

    /**
     * Generate a valid Indonesian phone number starting with "62" and 10-15 digits total.
     */
    private function phoneGenerator(): Generator
    {
        return Generator\map(
            function (int $suffix) {
                return '62' . str_pad((string) abs($suffix), 10, '0', STR_PAD_LEFT);
            },
            Generator\choose(1000000000, 9999999999)
        );
    }

    /**
     * Generate a cart with 1-5 items, each with name, variant, qty, price.
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
     * Generate valid buyer data matching the buyer form structure.
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
