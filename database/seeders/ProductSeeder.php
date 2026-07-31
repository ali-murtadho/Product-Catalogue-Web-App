<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $elektronik = Category::where('name', 'Elektronik')->first();
        $fashion = Category::where('name', 'Fashion')->first();
        $makanan = Category::where('name', 'Makanan & Minuman')->first();
        $kesehatan = Category::where('name', 'Kesehatan & Kecantikan')->first();
        $rumahTangga = Category::where('name', 'Rumah Tangga')->first();

        $products = [
            [
                'category' => $elektronik,
                'name' => 'Wireless Earbuds Pro',
                'description' => 'Earbuds nirkabel dengan noise cancellation dan baterai tahan 24 jam.',
                'price' => 350000,
                'discount_price' => 299000,
                'stock_quantity' => 50,
                'is_unlimited' => false,
                'is_featured' => true,
                'images' => ['products/placeholder-1.jpg', 'products/placeholder-2.jpg'],
                'variants' => [
                    ['variant_name' => 'Warna', 'variant_value' => 'Hitam', 'price_impact' => 0, 'stock_quantity' => 30],
                    ['variant_name' => 'Warna', 'variant_value' => 'Putih', 'price_impact' => 0, 'stock_quantity' => 20],
                ],
            ],
            [
                'category' => $elektronik,
                'name' => 'Charger Fast Charging 65W',
                'description' => 'Charger multi-port dengan teknologi fast charging GaN.',
                'price' => 180000,
                'discount_price' => null,
                'stock_quantity' => 100,
                'is_unlimited' => false,
                'is_featured' => false,
                'images' => ['products/placeholder-3.jpg'],
                'variants' => [],
            ],
            [
                'category' => $fashion,
                'name' => 'Kaos Polos Premium',
                'description' => 'Kaos cotton combed 30s, nyaman dipakai sehari-hari.',
                'price' => 89000,
                'discount_price' => 75000,
                'stock_quantity' => 0,
                'is_unlimited' => true,
                'is_featured' => true,
                'images' => ['products/placeholder-4.jpg', 'products/placeholder-5.jpg'],
                'variants' => [
                    ['variant_name' => 'Ukuran', 'variant_value' => 'S', 'price_impact' => 0, 'stock_quantity' => 25],
                    ['variant_name' => 'Ukuran', 'variant_value' => 'M', 'price_impact' => 0, 'stock_quantity' => 40],
                    ['variant_name' => 'Ukuran', 'variant_value' => 'L', 'price_impact' => 0, 'stock_quantity' => 35],
                    ['variant_name' => 'Ukuran', 'variant_value' => 'XL', 'price_impact' => 5000, 'stock_quantity' => 20],
                ],
            ],
            [
                'category' => $fashion,
                'name' => 'Topi Baseball Classic',
                'description' => 'Topi baseball dengan bahan twill cotton dan strap adjustable.',
                'price' => 55000,
                'discount_price' => null,
                'stock_quantity' => 2,
                'is_unlimited' => false,
                'is_featured' => false,
                'images' => ['products/placeholder-6.jpg'],
                'variants' => [],
            ],
            [
                'category' => $makanan,
                'name' => 'Kopi Arabika Gayo 250g',
                'description' => 'Biji kopi arabika single origin dari dataran tinggi Gayo, Aceh.',
                'price' => 95000,
                'discount_price' => 85000,
                'stock_quantity' => 30,
                'is_unlimited' => false,
                'is_featured' => true,
                'images' => ['products/placeholder-7.jpg'],
                'variants' => [
                    ['variant_name' => 'Jenis', 'variant_value' => 'Biji Utuh', 'price_impact' => 0, 'stock_quantity' => 15],
                    ['variant_name' => 'Jenis', 'variant_value' => 'Bubuk Halus', 'price_impact' => 5000, 'stock_quantity' => 15],
                ],
            ],
            [
                'category' => $makanan,
                'name' => 'Sambal Matah Bali',
                'description' => 'Sambal matah khas Bali dengan bahan segar tanpa pengawet.',
                'price' => 35000,
                'discount_price' => null,
                'stock_quantity' => 0,
                'is_unlimited' => false,
                'is_featured' => false,
                'images' => ['products/placeholder-8.jpg'],
                'variants' => [],
            ],
            [
                'category' => $kesehatan,
                'name' => 'Serum Vitamin C 20ml',
                'description' => 'Serum wajah dengan vitamin C 15% untuk mencerahkan kulit.',
                'price' => 125000,
                'discount_price' => 99000,
                'stock_quantity' => 45,
                'is_unlimited' => false,
                'is_featured' => true,
                'images' => ['products/placeholder-9.jpg', 'products/placeholder-10.jpg'],
                'variants' => [],
            ],
            [
                'category' => $rumahTangga,
                'name' => 'Lilin Aromaterapi Lavender',
                'description' => 'Lilin soy wax dengan aroma lavender alami, tahan 40 jam.',
                'price' => 75000,
                'discount_price' => null,
                'stock_quantity' => 20,
                'is_unlimited' => false,
                'is_featured' => false,
                'images' => ['products/placeholder-11.jpg'],
                'variants' => [
                    ['variant_name' => 'Ukuran', 'variant_value' => 'Small (100g)', 'price_impact' => 0, 'stock_quantity' => 12],
                    ['variant_name' => 'Ukuran', 'variant_value' => 'Large (200g)', 'price_impact' => 35000, 'stock_quantity' => 8],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $category = $productData['category'];
            if (!$category) {
                continue;
            }

            $product = Product::updateOrCreate(
                ['name' => $productData['name']],
                [
                    'category_id' => $category->id,
                    'slug' => Str::slug($productData['name']),
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'discount_price' => $productData['discount_price'],
                    'stock_quantity' => $productData['stock_quantity'],
                    'is_unlimited' => $productData['is_unlimited'],
                    'is_featured' => $productData['is_featured'],
                ]
            );

            // Seed images
            foreach ($productData['images'] as $index => $imagePath) {
                ProductImage::updateOrCreate(
                    ['product_id' => $product->id, 'image_path' => $imagePath],
                    [
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]
                );
            }

            // Seed variants
            foreach ($productData['variants'] as $variant) {
                ProductVariant::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'variant_name' => $variant['variant_name'],
                        'variant_value' => $variant['variant_value'],
                    ],
                    [
                        'price_impact' => $variant['price_impact'],
                        'stock_quantity' => $variant['stock_quantity'],
                    ]
                );
            }
        }
    }
}
