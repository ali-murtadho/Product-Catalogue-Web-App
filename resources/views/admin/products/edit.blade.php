<x-admin-layout>
    <x-slot name="header">Edit Produk</x-slot>

    <div class="bg-white rounded-lg shadow p-6 max-w-4xl">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $product->name) }}"
                           required
                           maxlength="255"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Masukkan nama produk">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id"
                            id="category_id"
                            required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number"
                           name="price"
                           id="price"
                           value="{{ old('price', $product->price) }}"
                           required
                           min="0"
                           step="1"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="0">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Discount Price -->
                <div>
                    <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Diskon (Rp) <span class="text-gray-400 font-normal">- opsional</span></label>
                    <input type="number"
                           name="discount_price"
                           id="discount_price"
                           value="{{ old('discount_price', $product->discount_price) }}"
                           min="0"
                           step="1"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="0">
                    @error('discount_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Quantity -->
                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Stok</label>
                    <input type="number"
                           name="stock_quantity"
                           id="stock_quantity"
                           value="{{ old('stock_quantity', $product->stock_quantity) }}"
                           required
                           min="0"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="0">
                    @error('stock_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Checkboxes -->
                <div class="flex items-center gap-6 pt-6">
                    <label class="inline-flex items-center">
                        <input type="hidden" name="is_unlimited" value="0">
                        <input type="checkbox"
                               name="is_unlimited"
                               value="1"
                               {{ old('is_unlimited', $product->is_unlimited) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Stok Unlimited</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox"
                               name="is_featured"
                               value="1"
                               {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Produk Featured</span>
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-gray-400 font-normal">- opsional</span></label>
                <textarea name="description"
                          id="description"
                          rows="4"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                          placeholder="Deskripsi produk...">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Existing Images -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
                @if ($product->images->count() > 0)
                    <div class="flex flex-wrap gap-4">
                        @foreach ($product->images as $image)
                            <div class="relative group">
                                <img src="{{ Storage::url($image->image_path) }}" alt="{{ $product->name }}" class="h-24 w-24 rounded-md object-cover border border-gray-200">
                                @if ($image->is_primary)
                                    <span class="absolute top-1 left-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">Utama</span>
                                @endif
                                <label class="absolute bottom-1 right-1 inline-flex items-center bg-white rounded px-1.5 py-0.5 shadow text-xs cursor-pointer">
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="rounded border-gray-300 text-red-600 mr-1">
                                    Hapus
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Centang "Hapus" untuk menghapus gambar.</p>
                @else
                    <p class="text-sm text-gray-500">Belum ada gambar.</p>
                @endif
            </div>

            <!-- Upload New Images -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tambah Gambar Baru
                    <span class="text-gray-400 font-normal">- maks {{ 5 - $product->images->count() }} gambar lagi</span>
                </label>
                <input type="file"
                       name="images[]"
                       id="images"
                       multiple
                       accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                       onchange="previewImages(event, {{ 5 - $product->images->count() }})">
                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WebP. Maksimum 2MB per gambar.</p>
                @error('images')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="image-preview" class="mt-3 flex flex-wrap gap-3 hidden">
                </div>
            </div>

            <!-- Existing Variants & Dynamic Add -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Varian Produk <span class="text-gray-400 font-normal">- opsional</span></label>
                    <button type="button" onclick="addVariantRow()" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Varian
                    </button>
                </div>
                <div id="variants-container">
                    @php
                        $variants = old('variants', $product->variants->map(function($v) {
                            return [
                                'id' => $v->id,
                                'variant_name' => $v->variant_name,
                                'variant_value' => $v->variant_value,
                                'price_impact' => $v->price_impact,
                                'stock_quantity' => $v->stock_quantity,
                            ];
                        })->toArray());
                    @endphp
                    @foreach ($variants as $index => $variant)
                        <div class="variant-row flex flex-wrap gap-3 items-start mb-3 p-3 bg-gray-50 rounded-md">
                            @if (!empty($variant['id']))
                                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] }}">
                            @endif
                            <div class="flex-1 min-w-[140px]">
                                <input type="text" name="variants[{{ $index }}][variant_name]" value="{{ $variant['variant_name'] ?? '' }}" placeholder="Nama varian (cth: Warna)" maxlength="100" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="flex-1 min-w-[140px]">
                                <input type="text" name="variants[{{ $index }}][variant_value]" value="{{ $variant['variant_value'] ?? '' }}" placeholder="Nilai (cth: Merah)" maxlength="100" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="w-32">
                                <input type="number" name="variants[{{ $index }}][price_impact]" value="{{ $variant['price_impact'] ?? '' }}" placeholder="± Harga" step="1" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="w-24">
                                <input type="number" name="variants[{{ $index }}][stock_quantity]" value="{{ $variant['stock_quantity'] ?? '' }}" placeholder="Stok" min="0" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <button type="button" onclick="removeVariantRow(this)" class="text-red-500 hover:text-red-700 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                @error('variants')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('variants.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="mt-8 flex items-center gap-4 border-t pt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Perbarui Produk
                </button>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>

    <script>
        let variantIndex = {{ count($variants) }};

        function addVariantRow() {
            const container = document.getElementById('variants-container');
            const row = document.createElement('div');
            row.className = 'variant-row flex flex-wrap gap-3 items-start mb-3 p-3 bg-gray-50 rounded-md';
            row.innerHTML = `
                <div class="flex-1 min-w-[140px]">
                    <input type="text" name="variants[${variantIndex}][variant_name]" placeholder="Nama varian (cth: Warna)" maxlength="100" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex-1 min-w-[140px]">
                    <input type="text" name="variants[${variantIndex}][variant_value]" placeholder="Nilai (cth: Merah)" maxlength="100" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="w-32">
                    <input type="number" name="variants[${variantIndex}][price_impact]" placeholder="± Harga" step="1" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="w-24">
                    <input type="number" name="variants[${variantIndex}][stock_quantity]" placeholder="Stok" min="0" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="button" onclick="removeVariantRow(this)" class="text-red-500 hover:text-red-700 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            container.appendChild(row);
            variantIndex++;
        }

        function removeVariantRow(button) {
            button.closest('.variant-row').remove();
        }

        function previewImages(event, maxAllowed) {
            const previewContainer = document.getElementById('image-preview');
            previewContainer.innerHTML = '';
            const files = event.target.files;

            if (files.length > maxAllowed) {
                alert(`Maksimal ${maxAllowed} gambar lagi yang dapat diunggah.`);
                event.target.value = '';
                previewContainer.classList.add('hidden');
                return;
            }

            if (files.length > 0) {
                previewContainer.classList.remove('hidden');
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'h-20 w-20 rounded-md object-cover border border-gray-200';
                        img.alt = 'Preview';
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.classList.add('hidden');
            }
        }
    </script>
</x-admin-layout>
