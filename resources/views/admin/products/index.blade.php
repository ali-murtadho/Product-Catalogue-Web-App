<x-admin-layout>
    <x-slot name="header">Produk</x-slot>

    <div class="bg-white rounded-lg shadow">
        <!-- Header with Add button -->
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Daftar Produk</h2>
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Produk
            </a>
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($products as $product)
                        <tr>
                            <!-- Gambar Thumbnail -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($product->primaryImage)
                                    <img src="{{ Storage::url($product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-md object-cover">
                                @else
                                    <div class="h-12 w-12 rounded-md bg-gray-200 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </td>

                            <!-- Nama -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $product->name }}
                            </td>

                            <!-- Kategori -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $product->category->name ?? '-' }}
                            </td>

                            <!-- Harga -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if ($product->discount_price)
                                    <span class="line-through text-gray-400">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    <br>
                                    <span class="text-green-600 font-medium">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                @else
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                @endif
                            </td>

                            <!-- Stok (Livewire StockUpdater) -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @livewire('stock-updater', ['productId' => $product->id, 'stock' => $product->stock_quantity, 'isUnlimited' => $product->is_unlimited], key('stock-'.$product->id))
                            </td>

                            <!-- Featured Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($product->is_featured)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Featured
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Belum ada produk. <a href="{{ route('admin.products.create') }}" class="text-indigo-600 hover:text-indigo-900">Tambah produk pertama</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($products->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
