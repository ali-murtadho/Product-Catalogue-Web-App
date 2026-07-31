<div class="relative" x-data="{ open: true }" @click.away="open = false">
    <!-- Search Input -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <input
            type="text"
            wire:model.live.debounce.300ms="query"
            @focus="open = true"
            placeholder="Cari produk..."
            class="w-full pl-10 pr-10 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
            autocomplete="off"
            aria-label="Cari produk"
        >
        @if ($query)
            <button
                wire:click="clearSearch"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                aria-label="Hapus pencarian"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading wire:target="query" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-3 z-50">
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>Mencari...</span>
        </div>
    </div>

    <!-- Search Results Dropdown -->
    @if ($results->count() > 0 && strlen($query) >= 2)
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden"
            wire:loading.remove
            wire:target="query"
        >
            <ul class="divide-y divide-gray-100" role="listbox" aria-label="Hasil pencarian">
                @foreach ($results as $product)
                    <li>
                        <a
                            href="{{ url('/produk/' . $product->slug) }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition"
                            role="option"
                        >
                            <!-- Product Image -->
                            <div class="flex-shrink-0 w-10 h-10 rounded-md overflow-hidden bg-gray-100">
                                @if ($product->primaryImage)
                                    <img
                                        src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $product->name }}
                                </p>
                                <div class="flex items-center gap-2">
                                    @if ($product->discount_price)
                                        <span class="text-sm font-semibold text-red-600">
                                            Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs text-gray-400 line-through">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-sm font-semibold text-gray-700">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Arrow Icon -->
                            <div class="flex-shrink-0 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @elseif (strlen($query) >= 2 && $results->count() === 0)
        <div
            x-show="open"
            class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 p-4"
            wire:loading.remove
            wire:target="query"
        >
            <p class="text-sm text-gray-500 text-center">Tidak ada produk yang ditemukan untuk "{{ $query }}"</p>
        </div>
    @endif
</div>
