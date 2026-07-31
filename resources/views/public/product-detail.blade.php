@extends('layouts.public')

@section('meta_title', $product->name . ' - ' . ($storeSetting->store_name ?? config('app.name')))
@section('meta_description', Str::limit(strip_tags($product->description), 160))
@section('meta_image', $product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : '')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="productDetail()">
        {{-- Breadcrumb --}}
        <nav class="mb-6 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ url('/') }}" class="hover:text-green-600">Beranda</a></li>
                <li><span>/</span></li>
                <li><a href="{{ url('/katalog') }}" class="hover:text-green-600">Katalog</a></li>
                @if($product->category)
                    <li><span>/</span></li>
                    <li><a href="{{ url('/katalog/' . $product->category->slug) }}" class="hover:text-green-600">{{ $product->category->name }}</a></li>
                @endif
                <li><span>/</span></li>
                <li class="text-gray-900 font-medium truncate">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            {{-- Photo Gallery --}}
            <div class="space-y-4">
                {{-- Main Image --}}
                <div class="relative aspect-square bg-gray-100 rounded-xl overflow-hidden shadow-sm">
                    @if(!$product->is_unlimited && $product->variants->isEmpty() && $product->stock_quantity == 0)
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-10">
                            <span class="bg-red-600 text-white text-lg font-bold px-6 py-3 rounded-lg">Stok Habis</span>
                        </div>
                    @elseif(!$product->is_unlimited && $product->variants->isNotEmpty())
                        {{-- For products with variants, show badge dynamically --}}
                        <div x-show="currentStock === 0 && !isUnlimited" x-cloak class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-10">
                            <span class="bg-red-600 text-white text-lg font-bold px-6 py-3 rounded-lg">Stok Habis</span>
                        </div>
                    @endif

                    @if($product->images->count() > 0)
                        @foreach($product->images as $index => $image)
                            <img
                                x-show="activeImage === {{ $index }}"
                                src="{{ asset('storage/' . $image->image_path) }}"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                            >
                        @endforeach
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Thumbnails --}}
                @if($product->images->count() > 1)
                    <div class="flex space-x-2 overflow-x-auto pb-2">
                        @foreach($product->images as $index => $image)
                            <button
                                @click="activeImage = {{ $index }}"
                                :class="activeImage === {{ $index }} ? 'ring-2 ring-green-600' : 'ring-1 ring-gray-200'"
                                class="flex-shrink-0 w-16 h-16 md:w-20 md:h-20 rounded-lg overflow-hidden focus:outline-none focus:ring-2 focus:ring-green-600"
                                aria-label="Lihat gambar {{ $index + 1 }}"
                            >
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }} - Gambar {{ $index + 1 }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="space-y-6">
                {{-- Product Name --}}
                <div>
                    @if($product->category)
                        <span class="text-sm text-green-600 font-medium">{{ $product->category->name }}</span>
                    @endif
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mt-1">{{ $product->name }}</h1>
                </div>

                {{-- Price --}}
                <div class="space-y-1">
                    @if($product->variants->isNotEmpty())
                        {{-- Dynamic price for variants --}}
                        <div>
                            @if($product->discount_price)
                                <span class="text-lg text-gray-400 line-through" x-text="formatPrice(originalPrice)"></span>
                                <span class="block text-2xl md:text-3xl font-bold text-green-600" x-text="formatPrice(activePrice)"></span>
                            @else
                                <span class="text-2xl md:text-3xl font-bold text-green-600" x-text="formatPrice(activePrice)"></span>
                            @endif
                        </div>
                    @else
                        {{-- Static price (no variants) --}}
                        <div>
                            @if($product->discount_price)
                                <span class="text-lg text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                <span class="block text-2xl md:text-3xl font-bold text-green-600">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                            @else
                                <span class="text-2xl md:text-3xl font-bold text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Stock Indicator --}}
                <div>
                    @if($product->is_unlimited)
                        <span class="inline-flex items-center text-sm text-green-700 bg-green-50 px-3 py-1 rounded-full">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Stok Tersedia
                        </span>
                    @elseif($product->variants->isNotEmpty())
                        {{-- Dynamic stock for variants --}}
                        <span x-show="isUnlimited" x-cloak class="inline-flex items-center text-sm text-green-700 bg-green-50 px-3 py-1 rounded-full">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Stok Tersedia
                        </span>
                        <span x-show="!isUnlimited && currentStock > 2" x-cloak class="inline-flex items-center text-sm text-green-700 bg-green-50 px-3 py-1 rounded-full">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Stok Tersedia: <span x-text="currentStock" class="ml-1"></span> pcs
                        </span>
                        <span x-show="!isUnlimited && currentStock > 0 && currentStock <= 2" x-cloak class="inline-flex items-center text-sm text-red-700 bg-red-50 px-3 py-1 rounded-full font-semibold">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            Sisa <span x-text="currentStock"></span> pcs lagi!
                        </span>
                        <span x-show="!isUnlimited && currentStock === 0" x-cloak class="inline-flex items-center text-sm text-red-700 bg-red-50 px-3 py-1 rounded-full font-semibold">
                            Stok Habis
                        </span>
                    @else
                        {{-- Static stock (no variants) --}}
                        @if($product->stock_quantity > 2)
                            <span class="inline-flex items-center text-sm text-green-700 bg-green-50 px-3 py-1 rounded-full">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Stok Tersedia: {{ $product->stock_quantity }} pcs
                            </span>
                        @elseif($product->stock_quantity > 0)
                            <span class="inline-flex items-center text-sm text-red-700 bg-red-50 px-3 py-1 rounded-full font-semibold">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                Sisa {{ $product->stock_quantity }} pcs lagi!
                            </span>
                        @else
                            <span class="inline-flex items-center text-sm text-red-700 bg-red-50 px-3 py-1 rounded-full font-semibold">
                                Stok Habis
                            </span>
                        @endif
                    @endif
                </div>

                {{-- Variant Selector --}}
                @if($product->variants->isNotEmpty())
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Pilih Varian</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->variants as $index => $variant)
                                <button
                                    @click="selectVariant({{ $index }})"
                                    :class="selectedVariant === {{ $index }} ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:border-green-600'"
                                    class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
                                >
                                    {{ $variant->variant_name }}: {{ $variant->variant_value }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Description --}}
                @if($product->description)
                    <div class="border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi Produk</h2>
                        <div class="prose prose-sm max-w-none text-gray-600">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="border-t pt-6 space-y-3">
                    <button
                        type="button"
                        :disabled="outOfStock"
                        :class="outOfStock ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                        class="w-full text-white font-semibold py-3 px-6 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
                    >
                        <span class="inline-flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                            </svg>
                            Tambah ke Keranjang
                        </span>
                    </button>

                    <a
                        :href="outOfStock ? '#' : waLink"
                        :class="outOfStock ? 'bg-gray-200 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-white text-green-600 border-green-600 hover:bg-green-50'"
                        class="w-full inline-flex items-center justify-center border-2 font-semibold py-3 px-6 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Beli Langsung via WA
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    function productDetail() {
        const basePrice = {{ $product->price }};
        const discountPrice = {{ $product->discount_price ?? 'null' }};
        const isProductUnlimited = {{ $product->is_unlimited ? 'true' : 'false' }};
        const productStock = {{ $product->stock_quantity }};
        const hasVariants = {{ $product->variants->isNotEmpty() ? 'true' : 'false' }};
        const productName = @json($product->name);
        const waNumber = @json($storeSetting->wa_numbers[0] ?? '');

        const variants = @json($product->variants->map(fn($v) => [
            'name' => $v->variant_name,
            'value' => $v->variant_value,
            'price_impact' => (float) $v->price_impact,
            'stock_quantity' => $v->stock_quantity,
        ]));

        return {
            activeImage: 0,
            selectedVariant: hasVariants ? 0 : null,
            isUnlimited: isProductUnlimited,

            get effectiveBasePrice() {
                return discountPrice !== null ? discountPrice : basePrice;
            },

            get originalPrice() {
                if (!hasVariants) return basePrice;
                const impact = this.selectedVariant !== null ? variants[this.selectedVariant].price_impact : 0;
                return basePrice + impact;
            },

            get activePrice() {
                if (!hasVariants) {
                    return discountPrice !== null ? discountPrice : basePrice;
                }
                const impact = this.selectedVariant !== null ? variants[this.selectedVariant].price_impact : 0;
                return this.effectiveBasePrice + impact;
            },

            get currentStock() {
                if (isProductUnlimited) return 999;
                if (!hasVariants) return productStock;
                if (this.selectedVariant !== null) {
                    return variants[this.selectedVariant].stock_quantity;
                }
                return productStock;
            },

            get outOfStock() {
                if (isProductUnlimited) return false;
                return this.currentStock === 0;
            },

            get waLink() {
                if (!waNumber) return '#';
                let message = `Halo, saya tertarik dengan produk *${productName}*`;
                if (hasVariants && this.selectedVariant !== null) {
                    const v = variants[this.selectedVariant];
                    message += ` (${v.name}: ${v.value})`;
                }
                message += ` dengan harga Rp ${this.formatNumber(this.activePrice)}. Apakah masih tersedia?`;
                return `https://api.whatsapp.com/send?phone=${waNumber}&text=${encodeURIComponent(message)}`;
            },

            selectVariant(index) {
                this.selectedVariant = index;
            },

            formatPrice(amount) {
                return 'Rp ' + this.formatNumber(amount);
            },

            formatNumber(amount) {
                return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount);
            }
        };
    }
</script>
@endpush
