<div>
    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Sidebar Filters --}}
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-xl shadow-sm p-5 space-y-6 sticky top-20">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Filter</h3>
                    <button wire:click="resetFilters" class="text-sm text-green-600 hover:text-green-700 font-medium">
                        Reset
                    </button>
                </div>

                {{-- Category Filter --}}
                <div>
                    <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select
                        id="category-filter"
                        wire:model.live="category"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Price Range Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Harga</label>
                    <div class="flex items-center gap-2">
                        <input
                            type="number"
                            wire:model.live.debounce.500ms="min_price"
                            placeholder="Min"
                            min="0"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                            aria-label="Harga minimum"
                        >
                        <span class="text-gray-400">-</span>
                        <input
                            type="number"
                            wire:model.live.debounce.500ms="max_price"
                            placeholder="Max"
                            min="0"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                            aria-label="Harga maksimum"
                        >
                    </div>
                </div>

                {{-- Stock Filter --}}
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model.live="in_stock_only"
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                        >
                        <span class="text-sm text-gray-700">Hanya stok tersedia</span>
                    </label>
                </div>

                {{-- Sort --}}
                <div>
                    <label for="sort-filter" class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                    <select
                        id="sort-filter"
                        wire:model.live="sort_by"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                    >
                        <option value="terbaru">Terbaru</option>
                        <option value="termurah">Termurah</option>
                        <option value="termahal">Termahal</option>
                    </select>
                </div>
            </div>
        </aside>

        {{-- Product Grid --}}
        <div class="flex-1">
            {{-- Loading Indicator --}}
            <div wire:loading class="flex items-center justify-center py-8">
                <svg class="animate-spin h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="ml-2 text-sm text-gray-500">Memuat produk...</span>
            </div>

            <div wire:loading.remove>
                @if($products->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                        @foreach($products as $product)
                            <a href="{{ url('/produk/' . $product->slug) }}" class="group bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                                <div class="aspect-square overflow-hidden bg-gray-100">
                                    @if($product->primaryImage)
                                        <img
                                            src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                            alt="{{ $product->name }}"
                                            loading="lazy"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3 md:p-4">
                                    <h3 class="text-sm md:text-base font-medium text-gray-900 truncate group-hover:text-green-600 transition-colors">{{ $product->name }}</h3>
                                    @if($product->category)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $product->category->name }}</p>
                                    @endif
                                    <div class="mt-1">
                                        @if($product->discount_price)
                                            <span class="text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                            <span class="block text-sm md:text-base font-bold text-green-600">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-sm md:text-base font-bold text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                    {{-- Stock indicator --}}
                                    @if(!$product->is_unlimited && $product->stock_quantity <= 0)
                                        <span class="inline-block mt-1 text-xs text-red-500 font-medium">Stok Habis</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada produk ditemukan</h3>
                        <p class="text-sm text-gray-500">Coba ubah filter untuk menemukan produk yang Anda cari.</p>
                        <button wire:click="resetFilters" class="mt-4 inline-block text-green-600 hover:text-green-700 font-medium text-sm">
                            Reset Filter
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
