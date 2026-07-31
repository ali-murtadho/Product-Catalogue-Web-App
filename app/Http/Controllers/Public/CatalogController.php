<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Display the catalog page with all products.
     */
    public function index(): View
    {
        $categories = Category::ordered()->get();

        return view('public.catalog', [
            'categories' => $categories,
            'currentCategory' => null,
        ]);
    }

    /**
     * Display the catalog page filtered by category.
     */
    public function byCategory(Category $category): View
    {
        $categories = Category::ordered()->get();

        return view('public.catalog', [
            'categories' => $categories,
            'currentCategory' => $category,
        ]);
    }
}
