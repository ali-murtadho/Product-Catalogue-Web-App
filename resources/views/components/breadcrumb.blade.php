@props(['items' => []])

<nav class="text-sm text-gray-500" aria-label="Breadcrumb">
    <ol class="flex items-center flex-wrap gap-y-1">
        <li>
            <a href="{{ route('home') }}" class="hover:text-green-600 transition-colors inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="hidden sm:inline">Beranda</span>
            </a>
        </li>

        @foreach($items as $item)
            <li class="flex items-center">
                <svg class="w-4 h-4 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>

                @if(!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:text-green-600 transition-colors">{{ $item['label'] }}</a>
                @else
                    <span class="text-gray-900 font-medium truncate max-w-[200px]">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
