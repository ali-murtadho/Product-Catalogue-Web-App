<x-admin-layout>
    <x-slot name="header">Pengaturan Toko</x-slot>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Store Name -->
            <div class="mb-6">
                <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Toko</label>
                <input type="text" name="store_name" id="store_name"
                       value="{{ old('store_name', $setting->store_name) }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required>
                @error('store_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- WhatsApp Numbers (Dynamic) -->
            <div class="mb-6" id="wa-numbers-section">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                <p class="text-xs text-gray-500 mb-2">Format: dimulai dengan 62, panjang 10-15 digit (contoh: 6281234567890)</p>

                <div id="wa-numbers-container">
                    @php
                        $waNumbers = old('wa_numbers', $setting->wa_numbers ?? ['']);
                    @endphp
                    @foreach($waNumbers as $index => $number)
                        <div class="flex items-center gap-2 mb-2 wa-number-row">
                            <input type="text" name="wa_numbers[]"
                                   value="{{ $number }}"
                                   placeholder="6281234567890"
                                   class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                            @if($index > 0 || count($waNumbers) > 1)
                                <button type="button" onclick="removeWaNumber(this)"
                                        class="px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 text-sm">
                                    Hapus
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button type="button" onclick="addWaNumber()"
                        class="mt-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">
                    + Tambah Nomor
                </button>

                @error('wa_numbers')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('wa_numbers.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- WA Template -->
            <div class="mb-6">
                <label for="wa_template" class="block text-sm font-medium text-gray-700 mb-1">Template Pesan WhatsApp</label>
                <p class="text-xs text-gray-500 mb-2">Template ini digunakan sebagai format dasar pesan pesanan ke WhatsApp.</p>
                <textarea name="wa_template" id="wa_template" rows="6"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Contoh: Halo {store_name}, saya ingin memesan...">{{ old('wa_template', $setting->wa_template) }}</textarea>
                @error('wa_template')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div class="mb-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Toko</label>
                <textarea name="address" id="address" rows="3"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Masukkan alamat lengkap toko">{{ old('address', $setting->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Social Links -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Tautan Media Sosial</label>

                <div class="space-y-3">
                    <div>
                        <label for="social_instagram" class="block text-xs text-gray-500 mb-1">Instagram</label>
                        <input type="text" name="social_links[instagram]" id="social_instagram"
                               value="{{ old('social_links.instagram', $setting->social_links['instagram'] ?? '') }}"
                               placeholder="https://instagram.com/namatoko"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('social_links.instagram')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="social_tiktok" class="block text-xs text-gray-500 mb-1">TikTok</label>
                        <input type="text" name="social_links[tiktok]" id="social_tiktok"
                               value="{{ old('social_links.tiktok', $setting->social_links['tiktok'] ?? '') }}"
                               placeholder="https://tiktok.com/@namatoko"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('social_links.tiktok')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="social_facebook" class="block text-xs text-gray-500 mb-1">Facebook</label>
                        <input type="text" name="social_links[facebook]" id="social_facebook"
                               value="{{ old('social_links.facebook', $setting->social_links['facebook'] ?? '') }}"
                               placeholder="https://facebook.com/namatoko"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('social_links.facebook')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Logo Upload -->
            <div class="mb-6">
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo Toko</label>

                @if($setting->logo_path)
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-1">Logo saat ini:</p>
                        <img src="{{ Storage::url($setting->logo_path) }}" alt="Logo Toko"
                             class="h-20 w-20 object-contain rounded border">
                    </div>
                @endif

                <input type="file" name="logo" id="logo" accept="image/jpg,image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-gray-500">Format: JPG, JPEG, PNG, WEBP. Maks: 2MB</p>
                @error('logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <script>
        function addWaNumber() {
            const container = document.getElementById('wa-numbers-container');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 mb-2 wa-number-row';
            row.innerHTML = `
                <input type="text" name="wa_numbers[]"
                       placeholder="6281234567890"
                       class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required>
                <button type="button" onclick="removeWaNumber(this)"
                        class="px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 text-sm">
                    Hapus
                </button>
            `;
            container.appendChild(row);
            updateRemoveButtons();
        }

        function removeWaNumber(button) {
            const container = document.getElementById('wa-numbers-container');
            const rows = container.querySelectorAll('.wa-number-row');
            if (rows.length > 1) {
                button.closest('.wa-number-row').remove();
                updateRemoveButtons();
            }
        }

        function updateRemoveButtons() {
            const container = document.getElementById('wa-numbers-container');
            const rows = container.querySelectorAll('.wa-number-row');
            rows.forEach((row, index) => {
                const existingBtn = row.querySelector('button');
                if (rows.length === 1 && existingBtn) {
                    existingBtn.remove();
                } else if (rows.length > 1 && !existingBtn) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.onclick = function() { removeWaNumber(this); };
                    btn.className = 'px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 text-sm';
                    btn.textContent = 'Hapus';
                    row.appendChild(btn);
                }
            });
        }
    </script>
</x-admin-layout>
