<?php

namespace App\Livewire;

use App\Models\OrderLog;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Services\WhatsAppMessageBuilder;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\Attributes\Validate;

class CartManager extends Component
{
    public array $cart = [];

    #[Validate('required|min:3', message: ['required' => 'Nama pemesan wajib diisi.', 'min' => 'Nama pemesan minimal 3 karakter.'])]
    public string $name = '';

    #[Validate('required|min:10', message: ['required' => 'Nomor WhatsApp wajib diisi.', 'min' => 'Nomor WhatsApp minimal 10 digit.'])]
    public string $phone = '';

    #[Validate('required|min:10', message: ['required' => 'Alamat pengiriman wajib diisi.', 'min' => 'Alamat pengiriman minimal 10 karakter.'])]
    public string $address = '';

    #[Validate('nullable')]
    public string $notes = '';

    public function mount(): void
    {
        $this->cart = session('cart', []);
    }

    /**
     * Add item to cart with real-time stock validation.
     *
     * Validates: Requirements 4.1, 4.2, 4.3
     */
    public function addItem(int $productId, ?int $variantId = null, int $qty = 1): void
    {
        // Rate limit: 20 cart operations per minute per IP
        $key = 'cart:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            session()->flash('error', 'Terlalu banyak permintaan. Silakan coba lagi nanti.');
            return;
        }
        RateLimiter::hit($key, 60);

        $product = Product::with(['primaryImage', 'variants'])->find($productId);

        if (!$product) {
            session()->flash('error', 'Produk tidak ditemukan.');
            return;
        }

        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if (!$variant || $variant->product_id !== $product->id) {
                session()->flash('error', 'Varian produk tidak valid.');
                return;
            }
        }

        // Determine available stock
        $maxStock = $this->getAvailableStock($product, $variant);

        // Check if stock is available (unlimited products always pass)
        if (!$product->is_unlimited && $maxStock <= 0) {
            session()->flash('error', 'Stok produk habis.');
            return;
        }

        // Calculate final price
        $price = $this->calculateFinalPrice($product, $variant);

        // Check if item already exists in cart
        $existingIndex = $this->findCartItemIndex($productId, $variantId);

        if ($existingIndex !== null) {
            // Item exists - add qty but validate total doesn't exceed stock
            $newQty = $this->cart[$existingIndex]['qty'] + $qty;

            if (!$product->is_unlimited && $newQty > $maxStock) {
                session()->flash('error', "Kuantitas melebihi stok tersedia. Maksimum: {$maxStock} pcs.");
                // Set qty to max stock
                $this->cart[$existingIndex]['qty'] = $maxStock;
                $this->cart[$existingIndex]['max_stock'] = $maxStock;
                $this->syncSession();
                return;
            }

            $this->cart[$existingIndex]['qty'] = $newQty;
            $this->cart[$existingIndex]['max_stock'] = $maxStock;
        } else {
            // Validate requested qty doesn't exceed stock
            if (!$product->is_unlimited && $qty > $maxStock) {
                session()->flash('error', "Kuantitas melebihi stok tersedia. Maksimum: {$maxStock} pcs.");
                $qty = $maxStock;
            }

            // Build variant label
            $variantLabel = null;
            if ($variant) {
                $variantLabel = $variant->variant_name . ' ' . $variant->variant_value;
            }

            // Get image path
            $imagePath = $product->primaryImage?->image_path;

            // Add new item
            $this->cart[] = [
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'name' => $product->name,
                'variant' => $variantLabel,
                'price' => $price,
                'qty' => $qty,
                'max_stock' => $maxStock,
                'image' => $imagePath,
            ];
        }

        $this->syncSession();
        $this->dispatch('cart-updated');
        session()->flash('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Update quantity of a cart item.
     *
     * Validates: Requirements 4.2, 4.3
     */
    public function updateQty(int $index, int $qty): void
    {
        if (!isset($this->cart[$index])) {
            return;
        }

        $item = $this->cart[$index];

        // Validate qty > 0
        if ($qty <= 0) {
            session()->flash('error', 'Kuantitas harus lebih dari 0.');
            return;
        }

        // Get real-time stock from database
        $maxStock = $this->getRealTimeStock($item['product_id'], $item['variant_id']);

        // Validate qty doesn't exceed max stock
        if ($maxStock !== null && $qty > $maxStock) {
            session()->flash('error', "Kuantitas melebihi stok tersedia. Maksimum: {$maxStock} pcs.");
            $qty = $maxStock;
        }

        $this->cart[$index]['qty'] = $qty;
        $this->cart[$index]['max_stock'] = $maxStock ?? $item['max_stock'];
        $this->syncSession();
        $this->dispatch('cart-updated');
    }

    /**
     * Remove item from cart.
     *
     * Validates: Requirements 4.4
     */
    public function removeItem(int $index): void
    {
        if (!isset($this->cart[$index])) {
            return;
        }

        unset($this->cart[$index]);
        // Re-index the array
        $this->cart = array_values($this->cart);
        $this->syncSession();
        $this->dispatch('cart-updated');
        session()->flash('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Get subtotal for a specific cart item.
     *
     * Validates: Requirements 4.5
     */
    public function getSubtotal(int $index): float
    {
        if (!isset($this->cart[$index])) {
            return 0;
        }

        return $this->cart[$index]['price'] * $this->cart[$index]['qty'];
    }

    /**
     * Get grand total of all cart items.
     *
     * Validates: Requirements 4.5
     */
    public function getGrandTotal(): float
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }

    /**
     * Get cart item count.
     */
    public function getCartCount(): int
    {
        return count($this->cart);
    }

    /**
     * Send order to WhatsApp after validating buyer form.
     *
     * Validates: Requirements 4.6, 5.1, 5.2, 5.3, 5.4, 5.5
     */
    public function sendToWhatsApp()
    {
        // Rate limit: 5 order submissions per minute per IP
        $key = 'order:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            session()->flash('error', 'Terlalu banyak permintaan pengiriman pesanan. Silakan coba lagi nanti.');
            return;
        }
        RateLimiter::hit($key, 60);

        $this->validate();

        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja kosong.');
            return;
        }

        // Get store settings
        $settings = StoreSetting::instance();

        if (!$settings || empty($settings->wa_numbers)) {
            session()->flash('error', 'Pengaturan toko belum dikonfigurasi.');
            return;
        }

        $waNumbers = $settings->wa_numbers;
        $storeName = $settings->store_name;
        $waTemplate = $settings->wa_template;

        // WA number rotation using session-based round-robin
        $lastIndex = session('wa_last_index', 0);
        $selectedIndex = $lastIndex % count($waNumbers);
        $selectedPhone = $waNumbers[$selectedIndex];
        session(['wa_last_index' => $selectedIndex + 1]);

        // Build buyer data
        $buyerData = [
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'notes' => $this->notes,
        ];

        // Build WhatsApp URL
        $url = WhatsAppMessageBuilder::build(
            $selectedPhone,
            $storeName,
            $this->cart,
            $buyerData,
            $waTemplate
        );

        // Save order log
        OrderLog::create([
            'items_json' => $this->cart,
            'buyer_info_json' => $buyerData,
            'total_amount' => $this->getGrandTotal(),
            'wa_number_used' => $selectedPhone,
        ]);

        // Reset session cart
        session()->forget('cart');
        $this->cart = [];

        // Redirect to WhatsApp URL
        return $this->redirect($url, navigate: false);
    }

    /**
     * Get available stock for a product/variant.
     */
    private function getAvailableStock(Product $product, ?ProductVariant $variant): int
    {
        if ($product->is_unlimited) {
            return PHP_INT_MAX;
        }

        if ($variant) {
            return $variant->stock_quantity;
        }

        return $product->stock_quantity;
    }

    /**
     * Get real-time stock from database.
     * Returns null if product is unlimited.
     */
    private function getRealTimeStock(int $productId, ?int $variantId): ?int
    {
        $product = Product::find($productId);

        if (!$product) {
            return 0;
        }

        if ($product->is_unlimited) {
            return null;
        }

        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            return $variant ? $variant->stock_quantity : 0;
        }

        return $product->stock_quantity;
    }

    /**
     * Calculate final price for a product with optional variant.
     */
    private function calculateFinalPrice(Product $product, ?ProductVariant $variant): float
    {
        // Use discount price if available, otherwise regular price
        $basePrice = $product->discount_price !== null
            ? (float) $product->discount_price
            : (float) $product->price;

        // Add variant price impact
        if ($variant) {
            $basePrice += (float) $variant->price_impact;
        }

        return $basePrice;
    }

    /**
     * Find existing cart item index by product_id and variant_id.
     */
    private function findCartItemIndex(int $productId, ?int $variantId): ?int
    {
        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] === $productId && $item['variant_id'] === $variantId) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Sync cart state to session.
     */
    private function syncSession(): void
    {
        session(['cart' => $this->cart]);
    }

    public function render()
    {
        return view('livewire.cart-manager', [
            'grandTotal' => $this->getGrandTotal(),
            'cartCount' => $this->getCartCount(),
        ]);
    }
}
