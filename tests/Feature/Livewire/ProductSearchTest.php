<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ProductSearch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_successfully(): void
    {
        Livewire::test(ProductSearch::class)
            ->assertStatus(200)
            ->assertSee('Cari produk...');
    }

    public function test_search_returns_results_matching_name(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Kemeja Flanel Premium',
            'description' => 'Bahan katun berkualitas',
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celana Jeans',
            'description' => 'Celana denim import',
        ]);

        Livewire::test(ProductSearch::class)
            ->set('query', 'Kemeja')
            ->assertSee('Kemeja Flanel Premium')
            ->assertDontSee('Celana Jeans');
    }

    public function test_search_returns_results_matching_description(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Kemeja Polos',
            'description' => 'Bahan katun organik premium',
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celana Cargo',
            'description' => 'Bahan polyester tahan air',
        ]);

        Livewire::test(ProductSearch::class)
            ->set('query', 'katun')
            ->assertSee('Kemeja Polos')
            ->assertDontSee('Celana Cargo');
    }

    public function test_search_does_not_run_with_short_query(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Kemeja Batik',
        ]);

        Livewire::test(ProductSearch::class)
            ->set('query', 'K')
            ->assertDontSee('Kemeja Batik');
    }

    public function test_search_results_clear_when_query_is_empty(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Kemeja Batik',
        ]);

        Livewire::test(ProductSearch::class)
            ->set('query', 'Kemeja')
            ->assertSee('Kemeja Batik')
            ->set('query', '')
            ->assertDontSee('Kemeja Batik');
    }

    public function test_search_limits_results_to_six(): void
    {
        $category = Category::factory()->create();

        for ($i = 1; $i <= 8; $i++) {
            Product::factory()->create([
                'category_id' => $category->id,
                'name' => "Produk Test $i",
            ]);
        }

        $component = Livewire::test(ProductSearch::class)
            ->set('query', 'Produk Test');

        $this->assertLessThanOrEqual(6, $component->get('results')->count());
    }

    public function test_search_shows_discount_price_when_available(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Kemeja Diskon',
            'price' => 150000,
            'discount_price' => 120000,
        ]);

        Livewire::test(ProductSearch::class)
            ->set('query', 'Kemeja Diskon')
            ->assertSee('Kemeja Diskon')
            ->assertSee('Rp 120.000')
            ->assertSee('Rp 150.000');
    }

    public function test_clear_search_resets_query_and_results(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Kemeja Polos',
        ]);

        Livewire::test(ProductSearch::class)
            ->set('query', 'Kemeja')
            ->assertSee('Kemeja Polos')
            ->call('clearSearch')
            ->assertSet('query', '')
            ->assertDontSee('Kemeja Polos');
    }
}
