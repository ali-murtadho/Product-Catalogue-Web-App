<?php

namespace Tests\Unit\Services;

use App\Services\WhatsAppMessageBuilder;
use PHPUnit\Framework\TestCase;

class WhatsAppMessageBuilderTest extends TestCase
{
    private string $phone = '6281234567890';
    private string $storeName = 'Toko Test';

    private array $cart;
    private array $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cart = [
            [
                'product_id' => 1,
                'variant_id' => 3,
                'name' => 'Kemeja Flanel',
                'variant' => 'Ukuran L / Warna Hitam',
                'price' => 150000,
                'qty' => 2,
                'max_stock' => 10,
                'image' => 'products/kemeja-flanel-1.jpg',
            ],
            [
                'product_id' => 2,
                'variant_id' => null,
                'name' => 'Kaos Polos',
                'variant' => null,
                'price' => 75000,
                'qty' => 1,
                'max_stock' => 20,
                'image' => 'products/kaos-polos-1.jpg',
            ],
        ];

        $this->buyer = [
            'name' => 'John Doe',
            'phone' => '6289876543210',
            'address' => 'Jl. Merdeka No. 123, Jakarta',
            'notes' => 'Kirim sore ya',
        ];
    }

    public function test_build_returns_whatsapp_api_url(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);

        $this->assertStringStartsWith('https://api.whatsapp.com/send?phone=', $url);
    }

    public function test_build_contains_phone_number_in_url(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);

        $this->assertStringContainsString("phone={$this->phone}", $url);
    }

    public function test_build_contains_encoded_text_parameter(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);

        $this->assertStringContainsString('&text=', $url);
    }

    public function test_decoded_message_contains_store_name(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString($this->storeName, $message);
    }

    public function test_decoded_message_contains_all_product_names(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);
        $message = $this->extractDecodedMessage($url);

        foreach ($this->cart as $item) {
            $this->assertStringContainsString($item['name'], $message);
        }
    }

    public function test_decoded_message_contains_variant_info(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString('Ukuran L / Warna Hitam', $message);
    }

    public function test_decoded_message_contains_quantities(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString('2 x', $message);
        $this->assertStringContainsString('1 x', $message);
    }

    public function test_decoded_message_contains_total_estimation(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);
        $message = $this->extractDecodedMessage($url);

        // Total: (150000 * 2) + (75000 * 1) = 375000
        $this->assertStringContainsString('375.000', $message);
    }

    public function test_decoded_message_contains_buyer_name(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString($this->buyer['name'], $message);
    }

    public function test_decoded_message_contains_buyer_phone(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString($this->buyer['phone'], $message);
    }

    public function test_decoded_message_contains_buyer_address(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString($this->buyer['address'], $message);
    }

    public function test_decoded_message_contains_buyer_notes(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString($this->buyer['notes'], $message);
    }

    public function test_message_omits_notes_when_empty(): void
    {
        $buyer = $this->buyer;
        $buyer['notes'] = '';

        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $buyer);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringNotContainsString('Catatan:', $message);
    }

    public function test_build_with_custom_template(): void
    {
        $template = "Halo {store_name}!\n\nPesanan:\n{order_details}\n\nTotal: {total}\n\nDari: {buyer_name}\nWA: {buyer_phone}\nAlamat: {buyer_address}\nCatatan: {buyer_notes}";

        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer, $template);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString("Halo {$this->storeName}!", $message);
        $this->assertStringContainsString($this->buyer['name'], $message);
        $this->assertStringContainsString('375.000', $message);
    }

    public function test_build_with_single_item_cart(): void
    {
        $cart = [
            [
                'product_id' => 1,
                'variant_id' => null,
                'name' => 'Produk Test',
                'variant' => null,
                'price' => 50000,
                'qty' => 3,
                'max_stock' => 5,
                'image' => 'products/test.jpg',
            ],
        ];

        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $cart, $this->buyer);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString('Produk Test', $message);
        $this->assertStringContainsString('150.000', $message); // 50000 * 3
    }

    public function test_build_uses_default_format_when_template_is_null(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer, null);
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString('Pesanan Baru', $message);
        $this->assertStringContainsString('Detail Pesanan', $message);
        $this->assertStringContainsString('Data Pemesan', $message);
    }

    public function test_build_uses_default_format_when_template_is_empty_string(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer, '');
        $message = $this->extractDecodedMessage($url);

        $this->assertStringContainsString('Pesanan Baru', $message);
    }

    public function test_url_text_is_properly_encoded(): void
    {
        $url = WhatsAppMessageBuilder::build($this->phone, $this->storeName, $this->cart, $this->buyer);

        // The text parameter should be URL-encoded (no raw spaces, newlines, etc.)
        $parts = parse_url($url);
        parse_str($parts['query'], $query);

        $this->assertArrayHasKey('phone', $query);
        $this->assertArrayHasKey('text', $query);
        $this->assertEquals($this->phone, $query['phone']);
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
