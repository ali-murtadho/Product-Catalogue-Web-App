<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex bg-gray-100">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-md flex-shrink-0">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ config('app.name', 'Laravel') }}
                    </h2>
                    <p class="text-sm text-gray-500">Admin Panel</p>
                </div>

                <nav class="mt-4">
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-indigo-700 bg-indigo-50 border-r-2 border-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.products.index') }}"
                               class="flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.products.*') ? 'text-indigo-700 bg-indigo-50 border-r-2 border-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Products
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index') }}"
                               class="flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.categories.*') ? 'text-indigo-700 bg-indigo-50 border-r-2 border-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Categories
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}"
                               class="flex items-center px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.settings.*') ? 'text-indigo-700 bg-indigo-50 border-r-2 border-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Store Settings
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- User Info & Logout -->
                <div class="absolute bottom-0 w-64 p-4 border-t bg-white">
                    <div class="flex items-center justify-between">
                        <div class="text-sm">
                            <p class="font-medium text-gray-700">{{ Auth::user()->name }}</p>
                            <p class="text-gray-500 text-xs">{{ Auth::user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-gray-600" title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <!-- Top Header -->
                <header class="bg-white shadow-sm border-b px-6 py-4">
                    <div class="flex items-center justify-between">
                        @isset($header)
                            <h1 class="text-xl font-semibold text-gray-800">{{ $header }}</h1>
                        @endisset
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-6">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
