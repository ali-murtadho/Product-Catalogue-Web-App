<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ProductDetailController extends Controller
{
    /**
     * Display the product detail page.
     */
    public function show(Product $product): View
    {
        $product->load([
            'images' => fn($query) => $query->orderBy('sort_order'),
            'variants',
            'category',
        ]);

        return view('public.product-detail', [
            'product' => $product,
        ]);
    }
}
