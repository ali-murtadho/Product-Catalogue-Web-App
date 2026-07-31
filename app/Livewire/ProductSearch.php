<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ProductSearch extends Component
{
    public string $query = '';
    public Collection $results;

    public function mount(): void
    {
        $this->results = collect();
    }

    public function updatedQuery(): void
    {
        $this->search();
    }

    public function search(): void
    {
        if (strlen(trim($this->query)) < 2) {
            $this->results = collect();
            return;
        }

        // Rate limit: 30 searches per minute per IP
        $key = 'search:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 30)) {
            $this->results = collect();
            return;
        }
        RateLimiter::hit($key, 60);

        $searchTerm = '%' . trim($this->query) . '%';

        $driver = Product::getConnectionResolver()
            ->connection()
            ->getDriverName();

        $operator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';

        $this->results = Product::where(function ($q) use ($searchTerm, $operator) {
            $q->where('name', $operator, $searchTerm)
              ->orWhere('description', $operator, $searchTerm);
        })
            ->with('primaryImage')
            ->limit(6)
            ->get();
    }

    public function clearSearch(): void
    {
        $this->query = '';
        $this->results = collect();
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}
