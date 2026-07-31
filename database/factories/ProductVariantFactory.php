<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'variant_name' => fake()->randomElement(['Ukuran', 'Warna', 'Rasa']),
            'variant_value' => fake()->randomElement(['S', 'M', 'L', 'XL', 'Merah', 'Biru', 'Hitam']),
            'price_impact' => fake()->randomFloat(2, 0, 50000),
            'stock_quantity' => fake()->numberBetween(1, 50),
        ];
    }
}
