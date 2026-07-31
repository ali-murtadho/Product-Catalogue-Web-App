<x-admin-layout>
    <x-slot name="header">Tambah Kategori</x-slot>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name') }}"
                       required
                       maxlength="255"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="Masukkan nama kategori">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image -->
            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar Ikon</label>
                <input type="file"
                       name="image"
                       id="image"
                       accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                       onchange="previewImage(event)">
                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WebP. Maksimum 2MB.</p>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="image-preview" class="mt-2 hidden">
                    <img id="preview-img" src="" alt="Preview" class="h-20 w-20 rounded-md object-cover">
                </div>
            </div>

            <!-- Sort Order -->
            <div class="mb-6">
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                <input type="number"
                       name="sort_order"
                       id="sort_order"
                       value="{{ old('sort_order', 0) }}"
                       min="0"
                       class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan Kategori
                </button>
                <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
</x-admin-layout>
