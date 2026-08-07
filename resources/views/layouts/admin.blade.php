<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin - {{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }" x-init="if(localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark')">
        <div class="min-h-screen flex bg-gray-100 dark:bg-gray-900">
            <!-- Mobile Sidebar Overlay -->
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden"
                @click="sidebarOpen = false"
                x-cloak
            ></div>

            <!-- Sidebar -->
            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-md flex-shrink-0 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:z-auto"
            >
                <div class="flex flex-col h-full">
                    <!-- Logo Header -->
                    <div class="p-4 border-b dark:border-gray-700 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                {{ config('app.name', 'Laravel') }}
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Admin Panel</p>
                        </div>
                        <!-- Close button (mobile only) -->
                        <button
                            @click="sidebarOpen = false"
                            class="lg:hidden p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            aria-label="Tutup sidebar"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 mt-4 overflow-y-auto">
                        <ul class="space-y-1 px-2">
                            <li>
                                <a href="{{ route('admin.dashboard') }}"
                                   class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-indigo-700 bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/30' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    {{ __('ui.dashboard') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.products.index') }}"
                                   class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.products.*') ? 'text-indigo-700 bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/30' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    {{ __('ui.products') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.categories.index') }}"
                                   class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.categories.*') ? 'text-indigo-700 bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/30' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ __('ui.categories') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.settings.index') }}"
                                   class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.settings.*') ? 'text-indigo-700 bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/30' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ __('ui.store_settings') }}
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <!-- User Info & Logout -->
                    <div class="p-4 border-t dark:border-gray-700 bg-white dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div class="text-sm min-w-0">
                                <p class="font-medium text-gray-700 dark:text-gray-200 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 flex-shrink-0" title="Logout">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top Header -->
                <header class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 px-4 sm:px-6 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Mobile menu button -->
                        <button
                            @click="sidebarOpen = true"
                            class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            aria-label="Buka sidebar"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        @isset($header)
                            <h1 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $header }}</h1>
                        @endisset

                        {{-- Language Switcher (Admin) --}}
                        <div class="flex items-center space-x-3">
                            {{-- Dark/Light Theme Toggle --}}
                            <button
                                x-data="{ dark: localStorage.getItem('theme') === 'dark' }"
                                x-init="$watch('dark', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', val) }); if(dark) document.documentElement.classList.add('dark')"
                                @click="dark = !dark"
                                class="p-2 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                title="Toggle theme"
                            >
                                {{-- Sun icon (shown in dark mode) --}}
                                <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                {{-- Moon icon (shown in light mode) --}}
                                <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                </svg>
                            </button>

                            {{-- Language Switcher --}}
                            <div class="relative" x-data="{ langOpen: false }">
                                <button @click="langOpen = !langOpen" class="flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                                    @if(app()->getLocale() === 'id')
                                        <svg class="w-5 h-4 rounded-sm shadow-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="640" height="240" fill="#e70011"/>
                                            <rect y="240" width="640" height="240" fill="#fff"/>
                                        </svg>
                                        <span class="ml-1.5">&nbsp;ID</span>
                                    @else
                                        <svg class="w-5 h-4 rounded-sm shadow-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="640" height="480" fill="#012169"/>
                                            <path d="M75 0l244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z" fill="#fff"/>
                                            <path d="M424 281l216 159v40L369 281h55zm-184 20l6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z" fill="#C8102E"/>
                                            <path d="M241 0v480h160V0H241zM0 160v160h640V160H0z" fill="#fff"/>
                                            <path d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z" fill="#C8102E"/>
                                        </svg>
                                        <span class="ml-1.5">&nbsp;EN</span>
                                    @endif
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="langOpen" @click.away="langOpen = false" x-cloak class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50">
                                    <a href="{{ route('language.switch', 'id') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'id' ? 'font-semibold bg-gray-50 dark:bg-gray-700' : '' }}">
                                        <svg class="w-5 h-4 mr-2 rounded-sm shadow-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="640" height="240" fill="#e70011"/>
                                            <rect y="240" width="640" height="240" fill="#fff"/>
                                        </svg>
                                        Indonesia
                                    </a>
                                    <a href="{{ route('language.switch', 'en') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'en' ? 'font-semibold bg-gray-50 dark:bg-gray-700' : '' }}">
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
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 overflow-x-auto">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
