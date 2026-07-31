<div class="inline-flex items-center space-x-2">
    @if ($isUnlimited)
        <span class="text-lg font-semibold text-green-600">&infin;</span>
        <span class="text-xs text-gray-400">Unlimited</span>
    @else
        <!-- Decrement Button -->
        <button
            wire:click="decrement"
            @if ($stock <= 0) disabled @endif
            class="inline-flex items-center justify-center w-7 h-7 rounded-md border text-sm font-medium transition
                {{ $stock <= 0 ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100 hover:text-gray-800' }}"
            title="Kurangi stok"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
        </button>

        <!-- Stock Display -->
        <span class="min-w-[2rem] text-center text-sm font-semibold {{ $stock <= 2 ? 'text-red-600' : 'text-gray-900' }}">
            {{ $stock }}
        </span>

        <!-- Increment Button -->
        <button
            wire:click="increment"
            class="inline-flex items-center justify-center w-7 h-7 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 hover:text-gray-800 text-sm font-medium transition"
            title="Tambah stok"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </button>

        <!-- Notification Flash -->
        @if ($showNotification)
            <span
                class="text-xs text-green-600 font-medium animate-pulse"
                x-data="{ show: true }"
                x-init="setTimeout(() => { show = false; $wire.set('showNotification', false) }, 2000)"
                x-show="show"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                {{ $notificationMessage }}
            </span>
        @endif
    @endif
</div>
