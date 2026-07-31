<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderLog;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $lowStockProducts = Product::lowStock()
            ->with('category')
            ->get();

        $lowStockVariants = ProductVariant::where('stock_quantity', '<=', 2)
            ->whereHas('product', function ($query) {
                $query->where('is_unlimited', false);
            })
            ->with('product')
            ->get();

        $orderLogs = OrderLog::latest()->paginate(10);

        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalOrderLogs = OrderLog::count();

        return view('admin.dashboard', compact(
            'lowStockProducts',
            'lowStockVariants',
            'orderLogs',
            'totalProducts',
            'totalCategories',
            'totalOrderLogs',
        ));
    }
}
