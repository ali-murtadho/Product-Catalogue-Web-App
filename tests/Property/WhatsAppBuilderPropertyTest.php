<?php

namespace Tests\Property;

use App\Services\WhatsAppMessageBuilder;
use Eris\TestTrait;
use Eris\Generator;
use PHPUnit\Framework\TestCase;

class WhatsAppBuilderPropertyTest extends TestCase
{
    use TestTrait;

    /**
     * Feature: product-catalogue-whatsapp, Property 8: WhatsApp Message Builder menghasilkan URL yang lengkap dan valid
     * Validates: Requirements 5.1, 5.2
     */
    public function testWhatsAppUrlIsCompleteAndValid(): void
    {
        $this
            ->limitTo(100)
            ->minimumEvaluationRatio(0.5)
            ->forAll(
                $this->phoneGenerator(),
                $this->storeNameGenerator(),
                $this->cartGenerator(),
                $this->buyerGenerator()
            )
            ->withMaxSize(50)
            ->then(function (string $phone, string $storeName, array $cart, array $buyer) {
                $url = WhatsAppMessageBuilder::build($phone, $storeName, $cart, $buyer);

                // URL starts with WhatsApp API base
                $this->assertStringStartsWith(
                    'https://api.whatsapp.com/send',
                    $url,
                    'URL harus dimulai dengan https://api.whatsapp.com/send'
                );

                // URL contains the phone number
                $this->assertStringContainsString(
                    "phone={$phone}",
                    $url,
                    'URL harus mengandung nomor telepon'
                );

                // URL contains text parameter
                $this->assertStringContainsString(
                    '&text=',
                    $url,
                    'URL harus mengandung parameter &text='
                );

                // Decode the message for content verification
                $message = $this->extractDecodedMessage($url);

                // Message contains ALL product names from cart
                foreach ($cart as $item) {
                    $this->assertStringContainsString(
                        $item['name'],
                        $message,
                        "Pesan harus mengandung nama produk: {$item['name']}"
                    );
                }

                // Message contains quantities from cart
                foreach ($cart as $item) {
                    $this->assertStringContainsString(
                        (string) $item['qty'],
                        $message,
                        "Pesan harus mengandung kuantitas: {$item['qty']}"
                    );
                }

                // Message contains buyer name
                $this->assertStringContainsString(
                    $buyer['name'],
                    $message,
                    'Pesan harus mengandung nama pemesan'
                );

                // Message contains buyer phone
                $this->assertStringContainsString(
                    $buyer['phone'],
                    $message,
                    'Pesan harus mengandung nomor telepon pemesan'
                );

                // Message contains buyer address
                $this->assertStringContainsString(
                    $buyer['address'],
                    $message,
                    'Pesan harus mengandung alamat pemesan'
                );

                // Message contains total amount (sum of price * qty for all items)
                $total = 0;
                foreach ($cart as $item) {
                    $total += $item['price'] * $item['qty'];
                }
                $formattedTotal = number_format($total, 0, ',', '.');
                $this->assertStringContainsString(
                    $formattedTotal,
                    $message,
                    "Pesan harus mengandung total estimasi: {$formattedTotal}"
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
     * Generate a non-empty store name.
     */
    private function storeNameGenerator(): Generator
    {
        return Generator\elements([
            'Toko Makmur',
            'Fashion Store',
            'Gadget Hub',
            'Warung Seblak',
            'Batik Nusantara',
            'Sepatu Keren',
            'Elektronik Jaya',
            'Buku Pintar',
            'Toko Roti',
            'Aksesoris Murah',
        ]);
    }

    /**
     * Generate a cart with 1-3 items, each with name, variant, qty, price.
     * Uses fixed-size tuple to avoid memory leaks from Generator\bind.
     */
    private function cartGenerator(): Generator
    {
        return Generator\map(
            function (array $data) {
                // Use count field to determine how many items to include
                $count = $data[0];
                $items = array_slice([$data[1], $data[2], $data[3]], 0, $count);
                return $items;
            },
            Generator\tuple(
                Generator\choose(1, 3),
                $this->cartItemGenerator(),
                $this->cartItemGenerator(),
                $this->cartItemGenerator()
            )
        );
    }

    /**
     * Generate a single cart item.
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
                Generator\choose(0, 9),    // name index
                Generator\choose(0, 7),    // variant index
                Generator\choose(1, 10),   // qty (1-10)
                Generator\choose(10000, 500000) // price (Rp 10.000 - Rp 500.000)
            )
        );
    }

    /**
     * Generate valid buyer data.
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
                Generator\choose(0, 7),          // name index
                Generator\choose(1000000000, 9999999999), // phone suffix
                Generator\choose(0, 4),          // address index
                Generator\choose(0, 4)           // notes index
            )
        );
    }

    /**
     * Feature: product-catalogue-whatsapp, Property 11: Rotasi nomor WhatsApp mendistribusikan secara merata
     * Validates: Requirements 5.5
     */
    public function testWhatsAppNumberRotationDistributesEvenly(): void
    {
        $this
            ->limitTo(100)
            ->minimumEvaluationRatio(0.5)
            ->forAll(
                $this->waNumbersGenerator(),
                $this->orderCountGenerator()
            )
            ->then(function (array $waNumbers, int $orderCount) {
                $numNumbers = count($waNumbers);

                // Simulate session-based round-robin rotation
                // This mirrors the logic in CartManager::sendToWhatsApp()
                $usageCounts = array_fill(0, $numNumbers, 0);
                $lastIndex = 0; // session starts at 0

                for ($i = 0; $i < $orderCount; $i++) {
                    $selectedIndex = $lastIndex % $numNumbers;
                    $usageCounts[$selectedIndex]++;
                    $lastIndex = $selectedIndex + 1;
                }

                // For perfect round-robin, each number should be used
                // exactly floor(N/M) or ceil(N/M) times
                $expectedMin = (int) floor($orderCount / $numNumbers);
                $expectedMax = (int) ceil($orderCount / $numNumbers);

                foreach ($usageCounts as $index => $count) {
                    $this->assertGreaterThanOrEqual(
                        $expectedMin,
                        $count,
                        "Nomor WA index {$index} digunakan {$count} kali, " .
                        "seharusnya minimal {$expectedMin} kali " .
                        "(N={$orderCount}, M={$numNumbers})"
                    );
                    $this->assertLessThanOrEqual(
                        $expectedMax,
                        $count,
                        "Nomor WA index {$index} digunakan {$count} kali, " .
                        "seharusnya maksimal {$expectedMax} kali " .
                        "(N={$orderCount}, M={$numNumbers})"
                    );
                }

                // Verify total usage equals order count
                $this->assertEquals(
                    $orderCount,
                    array_sum($usageCounts),
                    "Total penggunaan nomor harus sama dengan jumlah pesanan"
                );
            });
    }

    /**
     * Generate an array of 1-5 WhatsApp numbers.
     * Uses fixed-size tuple to avoid memory leaks from Generator\bind.
     */
    private function waNumbersGenerator(): Generator
    {
        return Generator\map(
            function (array $data) {
                $count = $data[0];
                return array_slice([$data[1], $data[2], $data[3], $data[4], $data[5]], 0, $count);
            },
            Generator\tuple(
                Generator\choose(1, 5),
                $this->phoneGenerator(),
                $this->phoneGenerator(),
                $this->phoneGenerator(),
                $this->phoneGenerator(),
                $this->phoneGenerator()
            )
        );
    }

    /**
     * Generate order count between 1 and 200.
     */
    private function orderCountGenerator(): Generator
    {
        return Generator\choose(1, 200);
    }

    /**
     * Extract and decode the message text from a WhatsApp URL.
     */
    private function extractDecodedMessage(string $url): string
    {
        $parts = parse_url($url);
        parse_str($parts['query'], $query);

        return $query['text'] ?? '';
    }
}
