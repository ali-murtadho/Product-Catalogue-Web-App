@extends('layouts.public')

@section('meta_title', 'Keranjang Belanja')
@section('meta_description', 'Lihat keranjang belanja Anda dan kirim pesanan via WhatsApp.')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Keranjang Belanja</h1>

        <livewire:cart-manager />
    </section>
@endsection
