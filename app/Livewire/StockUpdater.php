<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class StockUpdater extends Component
{
    public int $productId;
    public int $stock;
    public bool $isUnlimited;
    public bool $showNotification = false;
    public string $notificationMessage = '';

    public function mount(int $productId, int $stock, bool $isUnlimited): void
    {
        $this->productId = $productId;
        $this->stock = $stock;
        $this->isUnlimited = $isUnlimited;
    }

    public function increment(): void
    {
        if ($this->isUnlimited) {
            return;
        }

        $product = Product::findOrFail($this->productId);
        $product->increment('stock_quantity');
        $this->stock = $product->fresh()->stock_quantity;

        $this->flash('Stok bertambah');
    }

    public function decrement(): void
    {
        if ($this->isUnlimited) {
            return;
        }

        if ($this->stock <= 0) {
            return;
        }

        $product = Product::findOrFail($this->productId);

        if ($product->stock_quantity <= 0) {
            $this->stock = $product->stock_quantity;
            return;
        }

        $product->decrement('stock_quantity');
        $this->stock = $product->fresh()->stock_quantity;

        $this->flash('Stok berkurang');
    }

    public function setStock(int $value): void
    {
        if ($this->isUnlimited) {
            return;
        }

        $value = max(0, $value);

        $product = Product::findOrFail($this->productId);
        $product->update(['stock_quantity' => $value]);
        $this->stock = $value;

        $this->flash('Stok diperbarui');
    }

    private function flash(string $message): void
    {
        $this->showNotification = true;
        $this->notificationMessage = $message;

        $this->dispatch('stock-updated');
    }

    public function render()
    {
        return view('livewire.stock-updater');
    }
}
