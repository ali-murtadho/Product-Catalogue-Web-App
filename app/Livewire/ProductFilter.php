<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ProductFilter extends Component
{
    use WithPagination;

    public ?int $category = null;
    public ?float $min_price = null;
    public ?float $max_price = null;
    public bool $in_stock_only = false;
    public string $sort_by = 'terbaru';

    protected $queryString = [
        'category' => ['except' => null],
        'min_price' => ['except' => null],
        'max_price' => ['except' => null],
        'in_stock_only' => ['except' => false],
        'sort_by' => ['except' => 'terbaru'],
    ];

    /**
     * Reset pagination when any filter changes.
     */
    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatedMaxPrice(): void
    {
        $this->resetPage();
    }

    public function updatedInStockOnly(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    /**
     * Reset all filters to default.
     */
    public function resetFilters(): void
    {
        $this->reset(['category', 'min_price', 'max_price', 'in_stock_only', 'sort_by']);
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Product::query()->with('primaryImage', 'category');

        // Filter by category
        if ($this->category) {
            $query->where('category_id', $this->category);
        }

        // Filter by price range (consider discount_price as effective price)
        if ($this->min_price !== null) {
            $query->where(function ($q) {
                $q->whereNotNull('discount_price')
                  ->where('discount_price', '>=', $this->min_price)
                  ->orWhere(function ($q2) {
                      $q2->whereNull('discount_price')
                         ->where('price', '>=', $this->min_price);
                  });
            });
        }

        if ($this->max_price !== null) {
            $query->where(function ($q) {
                $q->whereNotNull('discount_price')
                  ->where('discount_price', '<=', $this->max_price)
                  ->orWhere(function ($q2) {
                      $q2->whereNull('discount_price')
                         ->where('price', '<=', $this->max_price);
                  });
            });
        }

        // Filter by stock availability
        if ($this->in_stock_only) {
            $query->where(function ($q) {
                $q->where('stock_quantity', '>', 0)
                  ->orWhere('is_unlimited', true);
            });
        }

        // Sorting
        switch ($this->sort_by) {
            case 'termurah':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'termahal':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'terbaru':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::ordered()->get();

        return view('livewire.product-filter', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
