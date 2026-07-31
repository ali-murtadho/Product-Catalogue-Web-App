<?php

namespace Tests\Feature\Admin;

use App\Livewire\StockUpdater;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StockUpdaterTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(array $attributes = []): Product
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'sort_order' => 1,
        ]);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'price' => 100000,
            'stock_quantity' => 10,
            'is_unlimited' => false,
            'is_featured' => false,
        ], $attributes));
    }

    public function test_stock_updater_renders_stock_value(): void
    {
        $product = $this->createProduct(['stock_quantity' => 5]);

        Livewire::test(StockUpdater::class, [
            'productId' => $product->id,
            'stock' => $product->stock_quantity,
            'isUnlimited' => false,
        ])->assertSee('5');
    }

    public function test_stock_updater_renders_unlimited_symbol(): void
    {
        $product = $this->createProduct(['is_unlimited' => true]);

        Livewire::test(StockUpdater::class, [
            'productId' => $product->id,
            'stock' => $product->stock_quantity,
            'isUnlimited' => true,
        ])->assertSee('Unlimited');
    }

    public function test_increment_increases_stock_by_one(): void
    {
        $product = $this->createProduct(['stock_quantity' => 5]);

        Livewire::test(StockUpdater::class, [
            'productId' => $product->id,
            'stock' => 5,
            'isUnlimited' => false,
        ])
            ->call('increment')
            ->assertSet('stock', 6);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 6,
        ]);
    }

    public function test_decrement_decreases_stock_by_one(): void
    {
        $product = $this->createProduct(['stock_quantity' => 5]);

        Livewire::test(StockUpdater::class, [
            'productId' => $product->id,
            'stock' => 5,
            'isUnlimited' => false,
        ])
            ->call('decrement')
            ->assertSet('stock', 4);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 4,
        ]);
    }

    public function test_decrement_does_not_go_below_zero(): void
    {
        $product = $this->createProduct(['stock_quantity' => 0]);

        Livewire::test(StockUpdater::class, [
            'productId' => $product->id,
            'stock' => 0,
            'isUnlimited' => false,
        ])
            ->call('decrement')
            ->assertSet('stock', 0);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 0,
        ]);
    }

    public function test_increment_is_noop_for_unlimited_products(): void
    {
        $product = $this->createProduct(['stock_quantity' => 10, 'is_unlimited' => true]);

        Livewire::test(StockUpdater::class, [
            'productId' => $product->id,
            'stock' => 10,
            'isUnlimited' => true,
        ])
            ->call('increment')
            ->assertSet('stock', 10);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10,
        ]);
    }

    public function test_decrement_is_noop_for_unlimited_products(): void
    {
        $product = $this->createProduct(['stock_quantity' => 10, 'is_unlimited' => true]);

        Livewire::test(StockUpdater::class, [
            'productId' => $product->id,
            'stock' => 10,
            'isUnlimited' => true,
        ])
            ->call('decrement')
            ->assertSet('stock', 10);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10,
        ]);
    }

    public function test_set_stock_updates_to_specific_value(): void
    {
        $product = $this->createProduct(['stock_quantity' => 5]);

        Livewire::test(StockUpdater::class, [
            'productId' => $product->id,
            'stock' => 5,
            'isUnlimited' => false,
        ])
            ->call('setStock', 20)
            ->assertSet('stock', 20);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 20,
        ]);
    }

    public function test_set_stock_clamps_negative_values_to_zero(): void
    {
        $product = $this->createProduct(['stock_quantity' => 5]);

        Livewire::test(StockUpdater::class, [
            'productId' => $product->id,
            'stock' => 5,
            'isUnlimited' => false,
        ])
            ->call('setStock', -5)
            ->assertSet('stock', 0);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 0,
        ]);
    }
}
