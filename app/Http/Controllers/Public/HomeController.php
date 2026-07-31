<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the homepage with categories, featured products, and latest products.
     */
    public function index(): View
    {
        $categories = Category::ordered()->get();

        $featuredProducts = Product::featured()
            ->inStock()
            ->with('primaryImage')
            ->limit(8)
            ->get();

        $latestProducts = Product::inStock()
            ->with('primaryImage')
            ->latest()
            ->limit(8)
            ->get();

        $storeSetting = StoreSetting::instance();

        return view('public.home', compact(
            'categories',
            'featuredProducts',
            'latestProducts',
            'storeSetting',
        ));
    }
}
