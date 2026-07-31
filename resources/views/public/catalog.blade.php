@extends('layouts.public')

@section('meta_title', ($currentCategory ? $currentCategory->name . ' - ' : '') . 'Katalog Produk')
@section('meta_description', ($currentCategory ? 'Jelajahi produk kategori ' . $currentCategory->name : 'Jelajahi semua produk di katalog kami') . '. Pesan langsung via WhatsApp.')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                @if($currentCategory)
                    {{ $currentCategory->name }}
                @else
                    Katalog Produk
                @endif
            </h1>

            {{-- Breadcrumb --}}
            <nav class="mt-2 text-sm text-gray-500" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ url('/') }}" class="hover:text-green-600 transition-colors">Beranda</a></li>
                    <li><span class="text-gray-300">/</span></li>
                    @if($currentCategory)
                        <li><a href="{{ url('/katalog') }}" class="hover:text-green-600 transition-colors">Katalog</a></li>
                        <li><span class="text-gray-300">/</span></li>
                        <li class="text-gray-900 font-medium">{{ $currentCategory->name }}</li>
                    @else
                        <li class="text-gray-900 font-medium">Katalog</li>
                    @endif
                </ol>
            </nav>
        </div>

        {{-- Category Pills (quick navigation) --}}
        @if($categories->count() > 0)
            <div class="mb-6 flex flex-wrap gap-2">
                <a
                    href="{{ url('/katalog') }}"
                    class="inline-block px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !$currentCategory ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Semua
                </a>
                @foreach($categories as $cat)
                    <a
                        href="{{ url('/katalog/' . $cat->slug) }}"
                        class="inline-block px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $currentCategory && $currentCategory->id === $cat->id ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Livewire Product Filter Component --}}
        @livewire('product-filter', ['category' => $currentCategory?->id])
    </section>
@endsection
