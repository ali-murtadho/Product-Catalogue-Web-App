<?php

namespace Tests\Feature\Livewire;

use App\Livewire\CartManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartManagerTest extends TestCase
{
    use RefreshDatabase;

    private function createProductWithImage(array $attributes = []): Product
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(array_merge(
            ['category_id' => $category->id],
            $attributes
        ));

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/test-image.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        return $product;
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::test(CartManager::class)
            ->assertStatus(200);
    }

    public function test_empty_cart_shows_empty_message(): void
    {
        Livewire::test(CartManager::class)
            ->assertSee('Keranjang belanja kosong');
    }

    public function test_add_item_to_cart(): void
    {
        $product = $this->createProductWithImage([
            'name' => 'Kemeja Flanel',
            'price' => 150000,
            'stock_quantity' => 10,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 2)
            ->assertSet('cart.0.product_id', $product->id)
            ->assertSet('cart.0.name', 'Kemeja Flanel')
            ->assertSet('cart.0.qty', 2)
            ->assertSet('cart.0.price', 150000.0)
            ->assertSet('cart.0.variant_id', null)
            ->assertSet('cart.0.variant', null)
            ->assertSet('cart.0.max_stock', 10)
            ->assertSet('cart.0.image', 'products/test-image.jpg');
    }

    public function test_add_item_with_variant(): void
    {
        $product = $this->createProductWithImage([
            'name' => 'Kemeja Flanel',
            'price' => 150000,
            'stock_quantity' => 0, // stock managed per variant
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'variant_name' => 'Ukuran',
            'variant_value' => 'L',
            'price_impact' => 10000,
            'stock_quantity' => 5,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, $variant->id, 1)
            ->assertSet('cart.0.product_id', $product->id)
            ->assertSet('cart.0.variant_id', $variant->id)
            ->assertSet('cart.0.variant', 'Ukuran L')
            ->assertSet('cart.0.price', 160000.0) // 150000 + 10000
            ->assertSet('cart.0.qty', 1)
            ->assertSet('cart.0.max_stock', 5);
    }

    public function test_add_item_with_discount_price(): void
    {
        $product = $this->createProductWithImage([
            'name' => 'Kemeja Diskon',
            'price' => 200000,
            'discount_price' => 150000,
            'stock_quantity' => 10,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 1)
            ->assertSet('cart.0.price', 150000.0); // uses discount_price
    }

    public function test_add_item_with_variant_and_discount_price(): void
    {
        $product = $this->createProductWithImage([
            'name' => 'Kemeja Diskon',
            'price' => 200000,
            'discount_price' => 150000,
            'stock_quantity' => 0,
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'variant_name' => 'Warna',
            'variant_value' => 'Hitam',
            'price_impact' => 5000,
            'stock_quantity' => 8,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, $variant->id, 1)
            ->assertSet('cart.0.price', 155000.0); // discount_price + price_impact
    }

    public function test_add_same_item_increases_quantity(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 10,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 2)
            ->call('addItem', $product->id, null, 3)
            ->assertSet('cart.0.qty', 5)
            ->assertCount('cart', 1);
    }

    public function test_add_same_item_validates_total_qty_against_stock(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 5,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 3)
            ->call('addItem', $product->id, null, 4)
            ->assertSet('cart.0.qty', 5); // capped at max stock
    }

    public function test_add_item_with_zero_stock_fails(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 0,
            'is_unlimited' => false,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 1)
            ->assertCount('cart', 0);
    }

    public function test_add_item_unlimited_stock_always_succeeds(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 0,
            'is_unlimited' => true,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 100)
            ->assertSet('cart.0.qty', 100)
            ->assertCount('cart', 1);
    }

    public function test_add_item_qty_exceeding_stock_is_capped(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 3,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 10)
            ->assertSet('cart.0.qty', 3); // capped to max_stock
    }

    public function test_add_invalid_product_shows_error(): void
    {
        Livewire::test(CartManager::class)
            ->call('addItem', 9999, null, 1)
            ->assertCount('cart', 0);
    }

    public function test_add_invalid_variant_shows_error(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 10,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, 9999, 1)
            ->assertCount('cart', 0);
    }

    public function test_update_qty_valid(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 10,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 2)
            ->call('updateQty', 0, 5)
            ->assertSet('cart.0.qty', 5);
    }

    public function test_update_qty_cannot_exceed_stock(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 5,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 2)
            ->call('updateQty', 0, 10)
            ->assertSet('cart.0.qty', 5); // capped at stock
    }

    public function test_update_qty_zero_shows_error(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 10,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 2)
            ->call('updateQty', 0, 0)
            ->assertSet('cart.0.qty', 2); // unchanged
    }

    public function test_update_qty_negative_shows_error(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 10,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 2)
            ->call('updateQty', 0, -1)
            ->assertSet('cart.0.qty', 2); // unchanged
    }

    public function test_remove_item(): void
    {
        $product1 = $this->createProductWithImage(['stock_quantity' => 10, 'name' => 'Product 1']);
        $product2 = $this->createProductWithImage(['stock_quantity' => 10, 'name' => 'Product 2']);

        Livewire::test(CartManager::class)
            ->call('addItem', $product1->id, null, 1)
            ->call('addItem', $product2->id, null, 1)
            ->assertCount('cart', 2)
            ->call('removeItem', 0)
            ->assertCount('cart', 1)
            ->assertSet('cart.0.name', 'Product 2'); // re-indexed
    }

    public function test_remove_item_re_indexes_array(): void
    {
        $product1 = $this->createProductWithImage(['stock_quantity' => 10, 'name' => 'A']);
        $product2 = $this->createProductWithImage(['stock_quantity' => 10, 'name' => 'B']);
        $product3 = $this->createProductWithImage(['stock_quantity' => 10, 'name' => 'C']);

        Livewire::test(CartManager::class)
            ->call('addItem', $product1->id, null, 1)
            ->call('addItem', $product2->id, null, 1)
            ->call('addItem', $product3->id, null, 1)
            ->call('removeItem', 1) // Remove 'B'
            ->assertCount('cart', 2)
            ->assertSet('cart.0.name', 'A')
            ->assertSet('cart.1.name', 'C'); // re-indexed from 2 to 1
    }

    public function test_grand_total_calculation(): void
    {
        $product1 = $this->createProductWithImage([
            'price' => 100000,
            'stock_quantity' => 10,
        ]);
        $product2 = $this->createProductWithImage([
            'price' => 50000,
            'stock_quantity' => 10,
        ]);

        $component = Livewire::test(CartManager::class)
            ->call('addItem', $product1->id, null, 2)  // 100000 * 2 = 200000
            ->call('addItem', $product2->id, null, 3); // 50000 * 3 = 150000

        // Grand total should be 350000 (check via view data)
        $cart = $component->get('cart');
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }
        $this->assertEquals(350000.0, $total);
    }

    public function test_cart_persists_in_session(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 10,
            'price' => 100000,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 2);

        // Verify session has cart data
        $cart = session('cart');
        $this->assertNotEmpty($cart);
        $this->assertEquals($product->id, $cart[0]['product_id']);
        $this->assertEquals(2, $cart[0]['qty']);
    }

    public function test_mount_loads_cart_from_session(): void
    {
        // Pre-set session cart
        session(['cart' => [
            [
                'product_id' => 1,
                'variant_id' => null,
                'name' => 'Test Product',
                'variant' => null,
                'price' => 100000,
                'qty' => 3,
                'max_stock' => 10,
                'image' => 'products/test.jpg',
            ],
        ]]);

        Livewire::test(CartManager::class)
            ->assertCount('cart', 1)
            ->assertSet('cart.0.name', 'Test Product')
            ->assertSet('cart.0.qty', 3);
    }

    public function test_dispatches_cart_updated_event_on_add(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 10,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 1)
            ->assertDispatched('cart-updated');
    }

    public function test_dispatches_cart_updated_event_on_remove(): void
    {
        $product = $this->createProductWithImage([
            'stock_quantity' => 10,
        ]);

        Livewire::test(CartManager::class)
            ->call('addItem', $product->id, null, 1)
            ->call('removeItem', 0)
            ->assertDispatched('cart-updated');
    }
}
