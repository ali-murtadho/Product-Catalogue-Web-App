# Website Katalog Produk & Pemesanan WhatsApp

Platform e-commerce ringan untuk menampilkan katalog produk dan menangani pemesanan langsung melalui WhatsApp. Tanpa payment gateway — semua transaksi via komunikasi WhatsApp.

## Tech Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Blade Templates + Livewire 3 + Tailwind CSS 3
- **Database:** PostgreSQL
- **Auth:** Laravel Breeze
- **Testing:** PHPUnit + Eris (Property-Based Testing)

## Fitur Utama

**Sisi Pembeli (Publik):**
- Beranda dengan banner, kategori, produk unggulan & terbaru
- Katalog produk dengan filter (kategori, harga, stok) dan sorting real-time
- Detail produk: galeri foto, varian, indikator stok, harga diskon
- Keranjang belanja berbasis session
- Pemesanan langsung via WhatsApp dengan pesan terformat otomatis
- Pencarian produk real-time
- Dual bahasa (Indonesia / English)

**Sisi Admin:**
- Dashboard: statistik, peringatan stok rendah, histori order
- CRUD produk (multi-image, varian, quick stock +/-)
- CRUD kategori dengan pengaturan urutan
- Pengaturan toko (nomor WA, template pesan, logo, sosial media)

## Instalasi

```bash
# Clone & install
git clone <repo-url>
cd product-catalogue-web-apps
composer install
npm install

# Konfigurasi
cp .env.example .env
php artisan key:generate
# Edit .env → set DB_CONNECTION=pgsql, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Database
php artisan migrate
php artisan db:seed

# Storage link (untuk gambar)
php artisan storage:link

# Build frontend assets
npm run build

# Jalankan
php artisan serve
```

## Menjalankan Aplikasi

**Production-ready (cukup 1 terminal):**
```bash
npm run build          # Build CSS/JS sekali
php artisan serve      # Jalankan server
```

**Development mode (2 terminal):**
```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev            # Hot-reload saat edit frontend
```

## Testing

```bash
php artisan test
```

96 tests, 2991 assertions (unit + feature + property-based tests).

## Struktur Penting

```
app/
├── Http/Controllers/Admin/     # CRUD admin
├── Http/Controllers/Public/    # Halaman publik
├── Http/Middleware/            # AdminMiddleware, SetLocale
├── Livewire/                   # CartManager, ProductFilter, StockUpdater, ProductSearch
├── Models/                     # Product, Category, ProductVariant, dll
└── Services/                   # WhatsAppMessageBuilder

lang/
├── id/ui.php                   # Terjemahan Indonesia
└── en/ui.php                   # Terjemahan English

resources/views/
├── layouts/                    # Layout publik & admin
├── public/                     # Halaman: home, catalog, product-detail, cart
├── admin/                      # Halaman admin: dashboard, products, categories, settings
└── livewire/                   # Komponen Livewire views
```

## Dokumentasi Lengkap

Lihat `DOKUMENTASI.md` untuk detail lengkap tentang halaman, database, alur pemesanan, dan kebutuhan data.

## Lisensi

MIT
