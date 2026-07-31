<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
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
