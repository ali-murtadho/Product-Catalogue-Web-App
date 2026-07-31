# Implementation Plan: Website Katalog Produk & Pemesanan WhatsApp

## Overview

Implementasi menggunakan Laravel 11 full-stack dengan Blade Templates, Livewire 3, Tailwind CSS 3, dan PostgreSQL. Setiap task membangun di atas task sebelumnya secara incremental. Property-based testing menggunakan library Eris untuk PHP.

## Tasks

- [x] 1. Setup project dan konfigurasi dasar
  - [x] 1.1 Inisialisasi project Laravel 11 dengan Breeze (Blade), Livewire 3, dan Tailwind CSS 3
    - Jalankan `composer create-project laravel/laravel`, install `livewire/livewire`, install `laravel/breeze --dev` dengan opsi blade
    - Konfigurasi `.env` untuk PostgreSQL (DB_CONNECTION=pgsql)
    - Install `giorgiosironi/eris` sebagai dev dependency untuk property testing
    - _Requirements: 12.1_

  - [x] 1.2 Buat migration files untuk seluruh tabel database
    - Buat migration: `users`, `categories`, `products`, `product_images`, `product_variants`, `store_settings`, `order_logs`
    - Terapkan constraints, foreign keys, dan index sesuai data model di design document
    - Kolom `wa_numbers` dan `social_links` di `store_settings` menggunakan tipe JSON
    - Kolom `items_json` dan `buyer_info_json` di `order_logs` menggunakan tipe JSON
    - _Requirements: Semua data model requirements_

  - [x] 1.3 Buat Eloquent Models dengan relationships dan business rules
    - Buat model: `Category`, `Product`, `ProductImage`, `ProductVariant`, `StoreSetting`, `OrderLog`
    - Definisikan relationships: Category hasMany Products, Product hasMany ProductImages, Product hasMany ProductVariants
    - Tambahkan casts untuk JSON columns, scopes untuk featured/in-stock/low-stock queries
    - Implementasi auto-slug generation di Product model menggunakan `Str::slug()` pada event `creating`/`updating`
    - _Requirements: 8.7, 11.1_

  - [ ]* 1.4 Write property test untuk slug generation
    - **Property 16: Slug generation menghasilkan format yang konsisten**
    - **Validates: Requirements 8.7**

- [x] 2. Implementasi Admin Authentication dan Middleware
  - [x] 2.1 Konfigurasi Laravel Breeze dan Admin Middleware
    - Buat `AdminMiddleware` yang memverifikasi user terautentikasi
    - Daftarkan middleware di `bootstrap/app.php`
    - Buat route group di `routes/admin.php` dengan prefix `admin` dan middleware `auth`, `admin`
    - Buat layout admin `resources/views/layouts/admin.blade.php` dengan sidebar navigation
    - _Requirements: 7.3, 12.1, 12.4, 12.5_

  - [ ]* 2.2 Write property test untuk auth guard
    - **Property 13: Auth guard menolak semua akses admin tanpa autentikasi**
    - **Validates: Requirements 7.3, 12.5**

- [ ] 3. Implementasi Manajemen Kategori (Admin)
  - [~] 3.1 Buat CategoryController (Admin) dengan CRUD lengkap
    - Implementasi `index`, `create`, `store`, `edit`, `update`, `destroy` methods
    - Validasi input: name (required, max:255), slug (auto-generate), image (nullable, max:2048kb, mimes:jpg,png,webp)
    - Upload gambar kategori ke `storage/app/public/categories/`
    - Implementasi sort_order management (drag-drop atau input angka)
    - Proteksi delete: cek apakah kategori memiliki produk terkait, tolak jika ada
    - _Requirements: 9.1, 9.2, 9.3, 9.4_

  - [x] 3.2 Buat Blade views untuk manajemen kategori admin
    - Buat `admin/categories/index.blade.php` — tabel daftar kategori dengan aksi edit/hapus dan pengaturan urutan
    - Buat `admin/categories/create.blade.php` dan `edit.blade.php` — form input dengan upload gambar
    - Gunakan Tailwind CSS untuk styling konsisten
    - _Requirements: 9.1, 9.2, 9.3_

  - [ ]* 3.3 Write property test untuk proteksi penghapusan kategori
    - **Property 17: Proteksi penghapusan kategori yang memiliki produk**
    - **Validates: Requirements 9.4**

  - [ ]* 3.4 Write property test untuk urutan kategori
    - **Property 18: Urutan kategori ditampilkan sesuai sort_order**
    - **Validates: Requirements 9.3**

- [x] 4. Implementasi Manajemen Produk (Admin)
  - [x] 4.1 Buat ProductController (Admin) dengan CRUD, multi-image, dan varian
    - Implementasi `index`, `create`, `store`, `edit`, `update`, `destroy` methods
    - Validasi: name, price, category_id (required), stock_quantity (integer >= 0), images (array, max 5, each max:2048kb)
    - Handle multi-image upload: simpan ke `storage/app/public/products/`, set gambar pertama sebagai `is_primary = true`
    - Handle varian management: CRUD varian inline di form produk (variant_name, variant_value, price_impact, stock_quantity)
    - Implementasi cascade delete: hapus produk → hapus images dari storage & DB, hapus variants
    - Implementasi kompresi gambar saat upload (resize max 1200px width, compress quality 80%)
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 11.3_

  - [x] 4.2 Buat Blade views untuk manajemen produk admin
    - Buat `admin/products/index.blade.php` — tabel produk dengan gambar thumbnail, harga, stok, status featured, aksi
    - Buat `admin/products/create.blade.php` dan `edit.blade.php` — form lengkap dengan multi-image upload preview, dynamic variant fields
    - Integrasikan Livewire `StockUpdater` component di index table untuk quick stock +/-
    - _Requirements: 8.1, 8.2, 8.3, 8.6_

  - [x] 4.3 Buat Livewire StockUpdater component
    - Buat `app/Livewire/StockUpdater.php` dengan methods `increment()` dan `decrement()`
    - Validasi: stok tidak boleh kurang dari 0 pada decrement
    - Update stok di database secara real-time tanpa page reload
    - Buat view `livewire/stock-updater.blade.php` dengan tombol +/- dan display stok current
    - _Requirements: 8.6_

  - [ ]* 4.4 Write property test untuk cascade delete produk
    - **Property 14: Cascade delete menghapus semua data terkait produk**
    - **Validates: Requirements 8.5**

  - [ ]* 4.5 Write property test untuk quick stock update
    - **Property 15: Quick stock update menghasilkan nilai yang benar**
    - **Validates: Requirements 8.6**

  - [ ]* 4.6 Write property test untuk invariant gambar primary
    - **Property 21: Invariant satu gambar primary per produk**
    - **Validates: Requirements 8.2**

- [x] 5. Checkpoint - Pastikan admin CRUD berfungsi
  - Pastikan semua migration berjalan tanpa error
  - Pastikan semua CRUD operations (kategori, produk, varian, gambar) bekerja
  - Jalankan seluruh tests yang sudah ditulis
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Implementasi Store Settings (Admin)
  - [x] 6.1 Buat StoreSettingController dan views
    - Implementasi `edit` dan `update` methods untuk singleton store settings
    - Form fields: store_name, wa_numbers (dynamic input untuk multiple nomor), wa_template (textarea), address, social_links (instagram, tiktok, facebook), logo upload
    - Validasi nomor WA: format harus dimulai dengan "62", panjang 10-15 digit
    - Buat seeder untuk initial store settings (row pertama)
    - _Requirements: 10.1, 10.2, 10.3_

  - [ ]* 6.2 Write property test untuk validasi nomor WhatsApp
    - **Property 19: Validasi nomor WhatsApp format internasional**
    - **Validates: Requirements 10.3**

- [ ] 7. Implementasi Dashboard Admin
  - [~] 7.1 Buat DashboardController dan view
    - Query produk/varian dengan stok <= 2 (dan is_unlimited = false) untuk low stock alerts
    - Query order_logs terbaru dengan pagination
    - Tampilkan statistik ringkas: total produk, total kategori, total order logs
    - Buat `admin/dashboard.blade.php` dengan cards untuk stats, tabel low-stock alerts, dan tabel order log history
    - _Requirements: 7.1, 7.2_

  - [ ]* 7.2 Write property test untuk peringatan stok rendah
    - **Property 12: Peringatan stok rendah menampilkan produk/varian yang benar**
    - **Validates: Requirements 7.1**

- [ ] 8. Implementasi Halaman Publik - Beranda
  - [~] 8.1 Buat HomeController dan halaman beranda
    - Buat `app/Http/Controllers/Public/HomeController.php`
    - Query: kategori (ordered by sort_order), produk featured (is_featured = true, limit 8), produk terbaru (latest, limit 8)
    - Buat layout publik `resources/views/layouts/app.blade.php` dengan header, footer, floating WA widget
    - Buat `resources/views/public/home.blade.php` dengan sections: hero/banner slider, kategori grid, produk unggulan, produk terbaru
    - Implementasi lazy loading pada gambar produk (`loading="lazy"`)
    - Implementasi meta tags untuk homepage
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 6.1, 6.3, 11.2, 11.4_

  - [~] 8.2 Buat Livewire ProductSearch component untuk pencarian real-time di beranda
    - Buat `app/Livewire/ProductSearch.php` dengan method `search()` — query produk by nama/deskripsi (LIKE atau full-text)
    - Debounce input 300ms menggunakan `wire:model.live.debounce.300ms`
    - Tampilkan dropdown hasil pencarian dengan gambar, nama, harga
    - Klik hasil → redirect ke halaman detail produk
    - _Requirements: 1.5_

  - [ ]* 8.3 Write property test untuk pencarian real-time
    - **Property 3: Pencarian real-time mengembalikan hasil yang relevan**
    - **Validates: Requirements 1.5**

- [ ] 9. Implementasi Katalog dan Filter Produk
  - [~] 9.1 Buat CatalogController dan halaman katalog
    - Buat `app/Http/Controllers/Public/CatalogController.php` dengan methods `index` dan `byCategory`
    - Route: `GET /katalog` (semua produk), `GET /katalog/{category:slug}` (per kategori)
    - Buat `resources/views/public/catalog.blade.php` dengan grid produk responsif (2 kolom mobile, 3-4 kolom desktop)
    - Integrasikan Livewire ProductFilter component
    - _Requirements: 2.1, 11.1_

  - [~] 9.2 Buat Livewire ProductFilter component
    - Buat `app/Livewire/ProductFilter.php` dengan properties: category, min_price, max_price, in_stock_only, sort_by
    - Implementasi query builder yang menerapkan semua filter secara dinamis
    - Filter kategori: where category_id = selected
    - Filter harga: where price between min and max (atau discount_price jika ada)
    - Filter stok: where stock_quantity > 0 OR is_unlimited = true
    - Sorting: orderBy created_at desc (terbaru), orderBy price asc (termurah), orderBy price desc (termahal)
    - Update tampilan real-time tanpa page reload via Livewire reactivity
    - Pagination menggunakan Livewire paginator
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6_

  - [ ]* 9.3 Write property test untuk filter katalog
    - **Property 1: Filter katalog mengembalikan hanya produk yang memenuhi semua kriteria filter**
    - **Validates: Requirements 2.2, 2.3, 2.4**

  - [ ]* 9.4 Write property test untuk pengurutan produk
    - **Property 2: Pengurutan produk menghasilkan urutan yang benar**
    - **Validates: Requirements 1.4, 2.5**

- [ ] 10. Implementasi Halaman Detail Produk
  - [~] 10.1 Buat ProductDetailController dan halaman detail produk
    - Buat `app/Http/Controllers/Public/ProductDetailController.php` dengan method `show`
    - Route: `GET /produk/{product:slug}`
    - Load product with relations: images (ordered by sort_order), variants
    - Buat `resources/views/public/product-detail.blade.php`:
      - Galeri foto dengan gambar utama besar dan thumbnail navigasi
      - Harga: tampilkan discount_price sebagai harga aktif + price coret jika diskon ada
      - Indikator stok: "Stok Tersedia: X pcs" atau "Sisa X pcs lagi!" (jika <= 2) dengan warna merah
      - Badge "Stok Habis" overlay pada gambar jika stok = 0 dan is_unlimited = false
      - Selector varian (dropdown/buttons) yang update harga dan stok secara dinamis
      - Tombol "Tambah ke Keranjang" (disabled jika stok habis)
      - Tombol "Beli Langsung via WA" (disabled jika stok habis)
    - Implementasi meta tags: title (nama produk), description (deskripsi produk), og:image (gambar primary)
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 11.1, 11.4_

  - [ ]* 10.2 Write property test untuk kalkulasi harga varian
    - **Property 4: Harga varian dihitung dengan benar**
    - **Validates: Requirements 3.6**

  - [ ]* 10.3 Write property test untuk tampilan harga diskon
    - **Property 20: Harga diskon ditampilkan dengan format coret yang benar**
    - **Validates: Requirements 3.2**

- [ ] 11. Implementasi Keranjang Belanja (Cart)
  - [~] 11.1 Buat Livewire CartManager component
    - Buat `app/Livewire/CartManager.php` dengan state: cart array, name, phone, address, notes
    - Method `addItem($productId, $variantId, $qty)`:
      - Validasi stok tersedia (query real-time dari DB)
      - Jika item sudah ada di cart, tambah qty (validasi total tidak melebihi stok)
      - Simpan ke session dengan struktur sesuai design document
    - Method `updateQty($index, $qty)`:
      - Validasi qty > 0 dan qty <= max_stock
      - Update session
    - Method `removeItem($index)`:
      - Hapus item dari array, re-index, update session
    - Computed properties: subtotals per item (price * qty), grand total
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [~] 11.2 Buat halaman keranjang dan form pemesan
    - Buat `CartController` dengan method `index` yang merender halaman keranjang
    - Buat `resources/views/public/cart.blade.php` dengan Livewire CartManager
    - Tampilkan: tabel item (gambar, nama, varian, harga satuan, qty input, subtotal, tombol hapus)
    - Form data pemesan: nama (required, min:3), nomor WA (required, min:10), alamat (required, min:10), catatan (optional)
    - Tombol "Kirim Pesanan via WhatsApp" — memanggil `sendToWhatsApp()`
    - Validasi form sisi client (Livewire validation) sebelum submit
    - _Requirements: 4.1, 4.4, 4.5, 4.6_

  - [ ]* 11.3 Write property test untuk validasi kuantitas keranjang
    - **Property 5: Kuantitas keranjang tidak pernah melebihi stok tersedia**
    - **Validates: Requirements 4.2, 4.3**

  - [ ]* 11.4 Write property test untuk kalkulasi total keranjang
    - **Property 6: Kalkulasi subtotal dan total keranjang selalu akurat**
    - **Validates: Requirements 4.5**

  - [ ]* 11.5 Write property test untuk validasi form pemesan
    - **Property 7: Validasi form pemesan menolak data yang tidak valid**
    - **Validates: Requirements 4.6**

- [ ] 12. Implementasi WhatsApp Message Builder dan Pengiriman Pesanan
  - [~] 12.1 Buat WhatsAppMessageBuilder service
    - Buat `app/Services/WhatsAppMessageBuilder.php`
    - Method `build(string $phone, string $storeName, array $cart, array $buyer, ?string $template = null): string`
    - Format pesan terstruktur: header toko, detail pesanan (no, nama, varian, qty, harga), separator, total estimasi, data pemesan (nama, WA, alamat, catatan)
    - Encode teks menggunakan `urlencode()`
    - Return full URL: `https://api.whatsapp.com/send?phone={phone}&text={encodedText}`
    - Jika template custom disediakan, gunakan template tersebut sebagai format dasar
    - _Requirements: 5.1, 5.2, 10.2_

  - [~] 12.2 Implementasi method sendToWhatsApp() di CartManager
    - Validasi form data pemesan
    - Ambil store settings (wa_numbers, store_name, wa_template)
    - Implementasi rotasi nomor WA: track penggunaan terakhir di session/cache, pilih nomor berikutnya
    - Panggil WhatsAppMessageBuilder::build()
    - Simpan OrderLog ke database (items_json, buyer_info_json, total_amount, wa_number_used)
    - Reset session cart
    - Return redirect ke WhatsApp URL
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

  - [ ]* 12.3 Write property test untuk WhatsApp Message Builder
    - **Property 8: WhatsApp Message Builder menghasilkan URL yang lengkap dan valid**
    - **Validates: Requirements 5.1, 5.2**

  - [ ]* 12.4 Write property test untuk order log akurasi
    - **Property 9: Order log mencatat snapshot pesanan secara akurat**
    - **Validates: Requirements 5.3**

  - [ ]* 12.5 Write property test untuk reset session setelah kirim
    - **Property 10: Session keranjang kosong setelah pengiriman pesanan**
    - **Validates: Requirements 5.4**

  - [ ]* 12.6 Write property test untuk rotasi nomor WhatsApp
    - **Property 11: Rotasi nomor WhatsApp mendistribusikan secara merata**
    - **Validates: Requirements 5.5**

- [~] 13. Checkpoint - Pastikan alur pemesanan end-to-end berfungsi
  - Pastikan alur lengkap: browse katalog → detail produk → tambah keranjang → kirim ke WA bekerja
  - Pastikan order log tercatat dengan benar
  - Jalankan seluruh tests
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 14. Implementasi Floating Widget dan Navigasi Kontak
  - [~] 14.1 Buat floating WhatsApp widget dan footer social links
    - Tambahkan floating widget di layout publik (`layouts/app.blade.php`): fixed position bottom-right, icon WA, link ke nomor CS dari store_settings
    - Tambahkan section footer: logo toko, alamat, tautan media sosial (Instagram, TikTok, Facebook) dari store_settings.social_links
    - Styling responsive: widget tidak mengganggu konten di mobile
    - _Requirements: 6.1, 6.2, 6.3_

- [ ] 15. Implementasi Keamanan dan Optimasi
  - [~] 15.1 Pastikan semua security measures terimplementasi
    - Verifikasi semua Blade templates menggunakan `{{ }}` (escape) bukan `{!! !!}` untuk user-generated content
    - Verifikasi semua form menggunakan `@csrf` directive
    - Verifikasi semua controller methods memiliki validation rules
    - Tambahkan rate limiting pada route publik yang sensitif (search, cart operations)
    - Konfigurasi `config/session.php` untuk security (httponly, secure, samesite)
    - _Requirements: 12.2, 12.3, 12.4_

  - [~] 15.2 Implementasi optimasi gambar dan performa
    - Tambahkan image compression pada upload (menggunakan Intervention Image atau GD library)
    - Validasi file size maksimum 2MB pada semua upload endpoints
    - Pastikan semua gambar di katalog/beranda memiliki `loading="lazy"`
    - Tambahkan database indexes pada kolom yang sering di-query: products.category_id, products.slug, categories.slug, products.is_featured
    - _Requirements: 11.2, 11.3_

- [ ] 16. Seeding dan Finalisasi
  - [~] 16.1 Buat database seeders untuk data awal
    - Buat `AdminUserSeeder`: admin user default
    - Buat `StoreSettingSeeder`: setting toko default dengan nomor WA contoh
    - Buat `CategorySeeder`: beberapa kategori contoh
    - Buat `ProductSeeder`: beberapa produk contoh dengan gambar placeholder, varian, dan stok
    - Daftarkan semua seeders di `DatabaseSeeder.php`
    - _Requirements: Semua (data awal untuk testing)_

  - [~] 16.2 Finalisasi routing dan navigasi
    - Pastikan semua routes terdaftar dan berfungsi (publik + admin)
    - Tambahkan breadcrumb navigation di halaman katalog dan detail produk
    - Pastikan navigasi admin sidebar highlight menu aktif
    - Verifikasi mobile responsiveness di semua halaman
    - _Requirements: 11.1_

- [~] 17. Final Checkpoint - Full system test
  - Jalankan seluruh test suite (unit + property + feature tests)
  - Verifikasi alur lengkap sebagai pembeli: beranda → katalog → filter → detail → keranjang → WA
  - Verifikasi alur lengkap sebagai admin: login → dashboard → CRUD produk → CRUD kategori → settings
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Setiap task mereferensi requirements spesifik untuk traceability
- Checkpoints di task 5, 13, dan 17 untuk validasi incremental
- Property tests memvalidasi correctness properties universal
- Unit/feature tests memvalidasi contoh spesifik dan edge cases
- Semua Livewire component harus diuji menggunakan `Livewire::test()` utilities
