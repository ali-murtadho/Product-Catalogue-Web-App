@extends('layouts.public')

@section('meta_title', __('ui.shopping_cart'))
@section('meta_description', __('ui.shopping_cart'))

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">{{ __('ui.shopping_cart') }}</h1>

        <livewire:cart-manager />
    </section>
@endsection
