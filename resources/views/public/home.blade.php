@extends('layouts.public')

@section('meta_title', ($storeSetting->store_name ?? config('app.name')) . ' - Katalog Produk & Pemesanan WhatsApp')
@section('meta_description', 'Jelajahi katalog produk terlengkap dan pesan langsung via WhatsApp. ' . ($storeSetting->store_name ?? config('app.name')))

@section('content')
    {{-- Hero / Banner Slider --}}
    <section class="relative bg-green-600 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="text-center text-white">
                <h1 class="text-3xl md:text-5xl font-bold mb-4">{{ __('ui.welcome_to', ['store' => $storeSetting->store_name ?? config('app.name')]) }}</h1>
                <p class="text-lg md:text-xl mb-8 text-green-100">{{ __('ui.hero_subtitle') }}</p>
                <a href="{{ url('/katalog') }}" class="inline-block bg-white text-green-600 font-semibold px-8 py-3 rounded-lg hover:bg-green-50 transition-colors">
                    {{ __('ui.view_catalog') }}
                </a>
            </div>
        </div>
        {{-- Decorative background --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-10 -right-10 w-72 h-72 bg-white rounded-full"></div>
            <div class="absolute -bottom-10 -left-10 w-96 h-96 bg-white rounded-full"></div>
        </div>
    </section>

    {{-- Kategori Grid --}}
    @if($categories->count() > 0)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8 text-center">{{ __('ui.product_categories') }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                    <a href="{{ url('/katalog/' . $category->slug) }}" class="group bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow text-center">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" loading="lazy" class="w-16 h-16 mx-auto mb-3 object-cover rounded-lg group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-16 h-16 mx-auto mb-3 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        @endif
                        <span class="text-sm font-medium text-gray-700 group-hover:text-green-600 transition-colors">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Produk Unggulan --}}
    @if($featuredProducts->count() > 0)
        <section class="bg-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8 text-center">{{ __('ui.featured_products') }}</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
                    @foreach($featuredProducts as $product)
                        <a href="{{ url('/produk/' . $product->slug) }}" class="group bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="aspect-square overflow-hidden bg-gray-100">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
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
                                <div class="mt-1">
                                    @if($product->discount_price)
                                        <span class="text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        <span class="block text-sm md:text-base font-bold text-green-600">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-sm md:text-base font-bold text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Produk Terbaru --}}
    @if($latestProducts->count() > 0)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8 text-center">{{ __('ui.latest_products') }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($latestProducts as $product)
                    <a href="{{ url('/produk/' . $product->slug) }}" class="group bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-square overflow-hidden bg-gray-100">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
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
                            <div class="mt-1">
                                @if($product->discount_price)
                                    <span class="text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    <span class="block text-sm md:text-base font-bold text-green-600">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-sm md:text-base font-bold text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
