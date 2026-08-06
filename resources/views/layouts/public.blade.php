<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Meta Tags --}}
    <title>@yield('meta_title', $storeSetting->store_name ?? config('app.name', 'Toko Online'))</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <meta name="description" content="@yield('meta_description', 'Katalog produk dan pemesanan via WhatsApp - ' . ($storeSetting->store_name ?? config('app.name')))">
    <meta property="og:title" content="@yield('meta_title', $storeSetting->store_name ?? config('app.name', 'Toko Online'))">
    <meta property="og:description" content="@yield('meta_description', 'Katalog produk dan pemesanan via WhatsApp')">
    <meta property="og:image" content="@yield('meta_image', $storeSetting->logo_path ? asset('storage/' . $storeSetting->logo_path) : '')">
    <meta property="og:type" content="website">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
    {{-- Header --}}
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo / Store Name --}}
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    @if($storeSetting->logo_path)
                        <img src="{{ asset('storage/' . $storeSetting->logo_path) }}" alt="{{ $storeSetting->store_name ?? 'Logo' }}" class="h-8 w-auto">
                    @endif
                    <span class="text-xl font-bold text-gray-900">{{ $storeSetting->store_name ?? config('app.name') }}</span>
                </a>

                {{-- Navigation --}}
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-green-600 transition-colors">{{ __('ui.home') }}</a>
                    <a href="{{ url('/katalog') }}" class="text-gray-600 hover:text-green-600 transition-colors">{{ __('ui.catalog') }}</a>
                    <a href="{{ url('/keranjang') }}" class="text-gray-600 hover:text-green-600 transition-colors">
                        <span class="inline-flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                            </svg>
                            {{ __('ui.cart') }}
                        </span>
                    </a>

                    {{-- Language Switcher --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-gray-600 hover:text-green-600 transition-colors text-sm">
                            @if(app()->getLocale() === 'id')
                                {{-- Bendera Indonesia --}}
                                <svg class="w-5 h-4 mr-1.5 rounded-sm shadow-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="640" height="240" fill="#e70011"/>
                                    <rect y="240" width="640" height="240" fill="#fff"/>
                                </svg>
                                <span>&nbsp;ID</span>
                            @else
                                {{-- Bendera UK --}}
                                <svg class="w-5 h-4 mr-1.5 rounded-sm shadow-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="640" height="480" fill="#012169"/>
                                    <path d="M75 0l244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z" fill="#fff"/>
                                    <path d="M424 281l216 159v40L369 281h55zm-184 20l6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z" fill="#C8102E"/>
                                    <path d="M241 0v480h160V0H241zM0 160v160h640V160H0z" fill="#fff"/>
                                    <path d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z" fill="#C8102E"/>
                                </svg>
                                <span>&nbsp;EN</span>
                            @endif
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <a href="{{ route('language.switch', 'id') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'id' ? 'font-semibold bg-gray-50' : '' }}">
                                <svg class="w-5 h-4 mr-2 rounded-sm shadow-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="640" height="240" fill="#e70011"/>
                                    <rect y="240" width="640" height="240" fill="#fff"/>
                                </svg>
                                Indonesia
                            </a>
                            <a href="{{ route('language.switch', 'en') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'en' ? 'font-semibold bg-gray-50' : '' }}">
                                <svg class="w-5 h-4 mr-2 rounded-sm shadow-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="640" height="480" fill="#012169"/>
                                    <path d="M75 0l244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z" fill="#fff"/>
                                    <path d="M424 281l216 159v40L369 281h55zm-184 20l6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z" fill="#C8102E"/>
                                    <path d="M241 0v480h160V0H241zM0 160v160h640V160H0z" fill="#fff"/>
                                    <path d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z" fill="#C8102E"/>
                                </svg>
                                English
                            </a>
                        </div>
                    </div>
                </nav>

                {{-- Mobile Menu Button --}}
                <button type="button" class="md:hidden p-2 rounded-md text-gray-600 hover:text-green-600 hover:bg-gray-100" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            {{-- Mobile Navigation --}}
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <a href="{{ url('/') }}" class="block py-2 text-gray-600 hover:text-green-600">{{ __('ui.home') }}</a>
                <a href="{{ url('/katalog') }}" class="block py-2 text-gray-600 hover:text-green-600">{{ __('ui.catalog') }}</a>
                <a href="{{ url('/keranjang') }}" class="block py-2 text-gray-600 hover:text-green-600">{{ __('ui.cart') }}</a>
                <div class="flex items-center space-x-3 pt-2 mt-2 border-t border-gray-200">
                    <span class="text-sm text-gray-500">{{ __('ui.language') }}:</span>
                    <a href="{{ route('language.switch', 'id') }}" class="inline-flex items-center text-sm {{ app()->getLocale() === 'id' ? 'font-bold text-green-600' : 'text-gray-600' }}">
                        <svg class="w-5 h-4 mr-1 rounded-sm shadow-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                            <rect width="640" height="240" fill="#e70011"/>
                            <rect y="240" width="640" height="240" fill="#fff"/>
                        </svg>
                        &nbsp;ID
                    </a>
                    <a href="{{ route('language.switch', 'en') }}" class="inline-flex items-center text-sm {{ app()->getLocale() === 'en' ? 'font-bold text-green-600' : 'text-gray-600' }}">
                        <svg class="w-5 h-4 mr-1 rounded-sm shadow-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                            <rect width="640" height="480" fill="#012169"/>
                            <path d="M75 0l244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z" fill="#fff"/>
                            <path d="M424 281l216 159v40L369 281h55zm-184 20l6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z" fill="#C8102E"/>
                            <path d="M241 0v480h160V0H241zM0 160v160h640V160H0z" fill="#fff"/>
                            <path d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z" fill="#C8102E"/>
                        </svg>
                        &nbsp;EN
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Store Info --}}
                <div>
                    @if($storeSetting->logo_path)
                        <img src="{{ asset('storage/' . $storeSetting->logo_path) }}" alt="{{ $storeSetting->store_name ?? 'Logo Toko' }}" class="h-12 w-auto mb-3">
                    @endif
                    <h3 class="text-white text-lg font-semibold mb-3">{{ $storeSetting->store_name ?? config('app.name') }}</h3>
                    @if($storeSetting->address)
                        <p class="text-sm">{{ $storeSetting->address }}</p>
                    @endif
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="text-white text-lg font-semibold mb-3">{{ __('ui.navigation') }}</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/') }}" class="hover:text-white transition-colors">{{ __('ui.home') }}</a></li>
                        <li><a href="{{ url('/katalog') }}" class="hover:text-white transition-colors">{{ __('ui.catalog') }}</a></li>
                        <li><a href="{{ url('/keranjang') }}" class="hover:text-white transition-colors">{{ __('ui.cart') }}</a></li>
                    </ul>
                </div>

                {{-- Social Links --}}
                <div>
                    <h3 class="text-white text-lg font-semibold mb-3">{{ __('ui.social_media') }}</h3>
                    <div class="flex space-x-4">
                        @if(!empty($storeSetting->social_links['instagram']))
                            <a href="{{ $storeSetting->social_links['instagram'] }}" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors" aria-label="Instagram">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </a>
                        @endif
                        @if(!empty($storeSetting->social_links['tiktok']))
                            <a href="{{ $storeSetting->social_links['tiktok'] }}" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors" aria-label="TikTok">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                            </a>
                        @endif
                        @if(!empty($storeSetting->social_links['facebook']))
                            <a href="{{ $storeSetting->social_links['facebook'] }}" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors" aria-label="Facebook">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-sm text-center">
                <p>&copy; {{ date('Y') }} {{ $storeSetting->store_name ?? config('app.name') }}. {{ __('ui.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    {{-- Floating WhatsApp Widget --}}
    @if(!empty($storeSetting->wa_numbers) && count($storeSetting->wa_numbers) > 0)
        <a href="https://api.whatsapp.com/send?phone={{ $storeSetting->wa_numbers[0] }}" target="_blank" rel="noopener noreferrer" class="fixed bottom-20 right-4 sm:bottom-6 sm:right-6 z-50 bg-green-500 hover:bg-green-600 text-white rounded-full p-3 sm:p-4 shadow-lg transition-transform hover:scale-110" aria-label="Chat via WhatsApp">
            <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>
    @endif

    @livewireScripts
    @stack('scripts')
</body>
</html>
