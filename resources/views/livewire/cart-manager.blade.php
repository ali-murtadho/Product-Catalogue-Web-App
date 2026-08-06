<div>
    {{-- Flash Messages --}}
    @if(session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(count($cart) === 0)
        {{-- Empty Cart State --}}
        <div class="text-center py-16">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
            </svg>
            <p class="text-gray-500 text-lg mb-4">{{ __('ui.cart_empty') }}</p>
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('ui.continue_shopping') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cart Items Section --}}
            <div class="lg:col-span-2">
                {{-- Desktop Table View --}}
                <div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">{{ __('ui.product') }}</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">{{ __('ui.price') }}</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">{{ __('ui.quantity') }}</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">{{ __('ui.subtotal') }}</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">{{ __('ui.action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($cart as $index => $item)
                                <tr class="hover:bg-gray-50" wire:key="cart-item-{{ $index }}">
                                    {{-- Product Info --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($item['image'])
                                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-14 h-14 object-cover rounded-lg border" loading="lazy">
                                            @else
                                                <div class="w-14 h-14 bg-gray-100 rounded-lg border flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $item['name'] }}</p>
                                                @if($item['variant'])
                                                    <p class="text-sm text-gray-500">{{ $item['variant'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Unit Price --}}
                                    <td class="px-4 py-4 text-center text-sm text-gray-700">
                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                    </td>

                                    {{-- Quantity Controls --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="updateQty({{ $index }}, {{ $item['qty'] - 1 }})"
                                                    class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-md hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                    @if($item['qty'] <= 1) disabled @endif
                                                    aria-label="Kurangi jumlah">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <span class="w-10 text-center font-medium text-gray-900">{{ $item['qty'] }}</span>
                                            <button wire:click="updateQty({{ $index }}, {{ $item['qty'] + 1 }})"
                                                    class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-md hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                    @if($item['qty'] >= $item['max_stock']) disabled @endif
                                                    aria-label="Tambah jumlah">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Subtotal --}}
                                    <td class="px-4 py-4 text-right font-semibold text-gray-900">
                                        Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                    </td>

                                    {{-- Delete Button --}}
                                    <td class="px-4 py-4 text-center">
                                        <button wire:click="removeItem({{ $index }})"
                                                wire:confirm="Hapus item ini dari keranjang?"
                                                class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-md transition-colors"
                                                aria-label="Hapus item {{ $item['name'] }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card View --}}
                <div class="md:hidden space-y-4">
                    @foreach($cart as $index => $item)
                        <div class="bg-white rounded-lg shadow p-4" wire:key="cart-mobile-{{ $index }}">
                            <div class="flex items-start gap-3">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded-lg border" loading="lazy">
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg border flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $item['name'] }}</p>
                                    @if($item['variant'])
                                        <p class="text-sm text-gray-500">{{ $item['variant'] }}</p>
                                    @endif
                                    <p class="text-sm text-gray-600 mt-1">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <button wire:click="removeItem({{ $index }})"
                                        wire:confirm="Hapus item ini dari keranjang?"
                                        class="text-red-500 hover:text-red-700 p-1"
                                        aria-label="Hapus item {{ $item['name'] }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between mt-3 pt-3 border-t">
                                <div class="flex items-center gap-2">
                                    <button wire:click="updateQty({{ $index }}, {{ $item['qty'] - 1 }})"
                                            class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-md hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                            @if($item['qty'] <= 1) disabled @endif
                                            aria-label="Kurangi jumlah">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="w-8 text-center font-medium">{{ $item['qty'] }}</span>
                                    <button wire:click="updateQty({{ $index }}, {{ $item['qty'] + 1 }})"
                                            class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-md hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                            @if($item['qty'] >= $item['max_stock']) disabled @endif
                                            aria-label="Tambah jumlah">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p class="font-semibold text-gray-900">
                                    Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Grand Total Summary --}}
                <div class="mt-6 bg-white rounded-lg shadow p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-700">{{ __('ui.total') }}</span>
                        <span class="text-xl font-bold text-green-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Buyer Information Form --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('ui.buyer_info') }}</h2>

                    <form wire:submit="sendToWhatsApp" class="space-y-4">
                        {{-- Nama Pemesan --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('ui.name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   wire:model.blur="name"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('name') border-red-500 @enderror"
                                   placeholder="{{ __('ui.name') }}">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nomor WhatsApp --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('ui.phone') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="phone"
                                   wire:model.blur="phone"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('phone') border-red-500 @enderror"
                                   placeholder="08123456789">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Alamat Pengiriman --}}
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('ui.address') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea id="address"
                                      wire:model.blur="address"
                                      rows="3"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('address') border-red-500 @enderror"
                                      placeholder="{{ __('ui.address') }}"></textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('ui.notes') }}
                            </label>
                            <textarea id="notes"
                                      wire:model.blur="notes"
                                      rows="2"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                                      placeholder="{{ __('ui.notes_placeholder') }}"></textarea>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-75 cursor-not-allowed">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="sendToWhatsApp">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading wire:target="sendToWhatsApp">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="sendToWhatsApp">{{ __('ui.send_order_wa') }}</span>
                            <span wire:loading wire:target="sendToWhatsApp">...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
