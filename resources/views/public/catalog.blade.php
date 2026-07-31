@extends('layouts.public')

@section('meta_title', ($currentCategory ? $currentCategory->name . ' - ' : '') . 'Katalog Produk')
@section('meta_description', ($currentCategory ? 'Jelajahi produk kategori ' . $currentCategory->name : 'Jelajahi semua produk di katalog kami') . '. Pesan langsung via WhatsApp.')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <div class="mb-6">
            <x-breadcrumb :items="$currentCategory
                ? [
                    ['label' => 'Katalog', 'url' => route('catalog.index')],
                    ['label' => $currentCategory->name],
                ]
                : [
                    ['label' => 'Katalog'],
                ]"
            />

            <h1 class="mt-3 text-2xl md:text-3xl font-bold text-gray-900">
                @if($currentCategory)
                    {{ $currentCategory->name }}
                @else
                    Katalog Produk
                @endif
            </h1>
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
