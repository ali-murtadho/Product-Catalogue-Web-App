# Dokumentasi Website Katalog Produk & Pemesanan WhatsApp

## Gambaran Umum

Website ini adalah platform e-commerce ringan yang memungkinkan pembeli menjelajahi katalog produk dan melakukan pemesanan langsung melalui WhatsApp. Tidak menggunakan payment gateway — semua transaksi ditangani melalui komunikasi WhatsApp antara pembeli dan penjual.

**Tech Stack:**
- Laravel 11 (PHP 8.2+)
- Blade Templates + Livewire 3
- Tailwind CSS 3
- PostgreSQL
- Laravel Breeze (autentikasi)

---

## Daftar Halaman

### A. Halaman Publik (Pembeli)

| No | Halaman | URL | Deskripsi |
|----|---------|-----|-----------|
| 1 | Beranda | `/` | Landing page dengan banner, kategori, produk unggulan, dan produk terbaru |
| 2 | Katalog | `/katalog` | Daftar semua produk dengan filter dan sorting |
| 3 | Katalog per Kategori | `/katalog/{slug-kategori}` | Produk difilter berdasarkan kategori tertentu |
| 4 | Detail Produk | `/produk/{slug-produk}` | Informasi lengkap produk: galeri, harga, stok, varian |
| 5 | Keranjang | `/keranjang` | Daftar item yang dipilih + form data pemesan + tombol kirim ke WA |

### B. Halaman Admin (Terproteksi Login)

| No | Halaman | URL | Deskripsi |
|----|---------|-----|-----------|
| 1 | Login | `/login` | Form login admin |
| 2 | Dashboard | `/admin` | Ringkasan: statistik, peringatan stok rendah, histori order |
| 3 | Daftar Produk | `/admin/products` | Tabel semua produk + quick stock update |
| 4 | Tambah Produk | `/admin/products/create` | Form tambah produk baru (multi-image, varian) |
| 5 | Edit Produk | `/admin/products/{id}/edit` | Form edit produk |
| 6 | Daftar Kategori | `/admin/categories` | Tabel kategori dengan pengaturan urutan |
| 7 | Tambah Kategori | `/admin/categories/create` | Form tambah kategori |
| 8 | Edit Kategori | `/admin/categories/{id}/edit` | Form edit kategori |
| 9 | Pengaturan Toko | `/admin/settings` | Konfigurasi nama toko, nomor WA, template pesan, logo, sosial media |

---

## Detail Halaman

### 1. Beranda (`/`)

**Konten:**
- Banner/slider promo
- Grid kategori produk (diurutkan berdasarkan `sort_order`)
- Produk unggulan (maks 8 item, filter `is_featured = true`)
- Produk terbaru (maks 8 item, urut tanggal terbaru)
- Kolom pencarian real-time (Livewire, debounce 300ms)
- Floating WhatsApp widget (pojok kanan bawah)
- Footer: logo, alamat, tautan sosial media

**Komponen Livewire:** `ProductSearch`

---

### 2. Katalog (`/katalog`)

**Konten:**
- Grid produk responsif (2 kolom mobile, 3-4 kolom desktop)
- Panel filter: kategori, rentang harga, ketersediaan stok
- Opsi sorting: terbaru, termurah, termahal
- Pagination
- Semua filter bekerja real-time tanpa reload halaman

**Komponen Livewire:** `ProductFilter`

---

### 3. Detail Produk (`/produk/{slug}`)

**Konten:**
- Galeri foto (gambar utama besar + thumbnail navigasi)
- Nama produk, deskripsi
- Harga: jika ada diskon → harga asli dicoret + harga diskon sebagai harga aktif
- Indikator stok: "Stok Tersedia: X pcs" atau "Sisa X pcs lagi!" (merah jika ≤ 2)
- Badge "Stok Habis" jika stok = 0 dan bukan unlimited
- Selector varian (dropdown/tombol) — mengubah harga & stok secara dinamis
- Tombol "Tambah ke Keranjang" (disabled jika stok habis)
- Tombol "Beli Langsung via WA" (disabled jika stok habis)
- Meta tags SEO: title, description, og:image

---

### 4. Keranjang (`/keranjang`)

**Konten:**
- Tabel item: gambar, nama, varian, harga satuan, qty (editable), subtotal, tombol hapus
- Total keseluruhan
- Form data pemesan:
  - Nama (wajib, min 3 karakter)
  - Nomor WhatsApp (wajib, min 10 digit)
  - Alamat pengiriman (wajib, min 10 karakter)
  - Catatan (opsional)
- Tombol "Kirim Pesanan via WhatsApp"

**Komponen Livewire:** `CartManager`

**Alur Kirim Pesanan:**
1. Validasi form pemesan
2. Format pesan via `WhatsAppMessageBuilder`
3. Simpan ke `order_logs`
4. Reset session keranjang
5. Redirect ke URL WhatsApp API

---

### 5. Dashboard Admin (`/admin`)

**Konten:**
- Kartu statistik: total produk, total kategori, total order
- Tabel peringatan stok rendah (produk/varian dengan stok ≤ 2)
- Tabel histori order log terbaru

---

### 6. Manajemen Produk (`/admin/products`)

**Fitur:**
- Daftar produk: thumbnail, nama, harga, stok, status featured, aksi (edit/hapus)
- Quick stock +/- via Livewire (tanpa reload)
- Form tambah/edit:
  - Nama, deskripsi, harga, harga diskon, kategori, stok, is_unlimited, is_featured
  - Upload multi-gambar (maks 5, maks 2MB per gambar, format: jpg/png/webp)
  - Manajemen varian inline (nama, nilai, dampak harga, stok per varian)
- Hapus produk: cascade delete (gambar + varian otomatis terhapus)

**Komponen Livewire:** `StockUpdater`

---

### 7. Manajemen Kategori (`/admin/categories`)

**Fitur:**
- Daftar kategori: gambar ikon, nama, jumlah produk, urutan, aksi
- Pengaturan urutan tampil (sort_order)
- Form tambah/edit: nama, gambar ikon (maks 2MB)
- Proteksi hapus: tidak bisa menghapus kategori yang masih memiliki produk

---

### 8. Pengaturan Toko (`/admin/settings`)

**Field yang bisa dikonfigurasi:**
- Nama toko
- Nomor WhatsApp (bisa multiple, untuk rotasi otomatis)
- Template pesan WhatsApp (dengan placeholder)
- Alamat toko
- Tautan sosial media (Instagram, TikTok, Facebook)
- Logo toko

---

## Struktur Database

### Diagram Relasi

```
users (admin)
    │
categories ──────────┐
    │                │
    └── products ────┤
            │        │
            ├── product_images
            └── product_variants

store_settings (singleton, 1 row)
order_logs (catatan pesanan)
```

### Tabel: `users`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT, PK | Auto increment |
| name | VARCHAR(255) | Nama admin |
| email | VARCHAR(255), UNIQUE | Email login |
| password | VARCHAR(255) | Bcrypt hash |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### Tabel: `categories`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT, PK | Auto increment |
| name | VARCHAR(255) | Nama kategori |
| slug | VARCHAR(255), UNIQUE | URL-friendly, auto-generated |
| image | VARCHAR(255), NULLABLE | Path gambar ikon |
| sort_order | INT, DEFAULT 0 | Urutan tampil di beranda |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Aturan bisnis:**
- Slug otomatis dari nama menggunakan `Str::slug()`
- Tidak bisa dihapus jika masih memiliki produk

---

### Tabel: `products`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT, PK | Auto increment |
| category_id | BIGINT, FK → categories | Relasi kategori |
| name | VARCHAR(255) | Nama produk |
| slug | VARCHAR(255), UNIQUE | URL-friendly, auto-generated |
| description | TEXT, NULLABLE | Deskripsi produk |
| price | DECIMAL(12,2) | Harga asli |
| discount_price | DECIMAL(12,2), NULLABLE | Harga diskon (null = tanpa diskon) |
| stock_quantity | INT, DEFAULT 0 | Jumlah stok |
| is_unlimited | BOOLEAN, DEFAULT false | Jika true, stok diabaikan (selalu tersedia) |
| is_featured | BOOLEAN, DEFAULT false | Tampil di produk unggulan beranda |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Aturan bisnis:**
- Jika `is_unlimited = true`, produk selalu bisa dipesan
- Jika `discount_price` ada, harus lebih kecil dari `price`
- Slug auto-generated, unik (auto-append angka jika duplikat)
- Saat dihapus, gambar dan varian ikut terhapus (cascade)

**Index:** `category_id`, `slug` (unique), `is_featured`

---

### Tabel: `product_images`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT, PK | Auto increment |
| product_id | BIGINT, FK → products (CASCADE DELETE) | Relasi produk |
| image_path | VARCHAR(255) | Path file di storage |
| is_primary | BOOLEAN, DEFAULT false | Gambar utama di katalog |
| sort_order | INT, DEFAULT 0 | Urutan galeri |
| created_at | TIMESTAMP | |

**Aturan bisnis:**
- Maksimal 5 gambar per produk
- Tepat 1 gambar wajib `is_primary = true` per produk
- File disimpan di `storage/app/public/products/`
- Ukuran maks 2MB, format: jpg, png, webp

---

### Tabel: `product_variants`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT, PK | Auto increment |
| product_id | BIGINT, FK → products (CASCADE DELETE) | Relasi produk |
| variant_name | VARCHAR(100) | Tipe varian (misal: Warna, Ukuran, Rasa) |
| variant_value | VARCHAR(100) | Nilai varian (misal: Merah, XL, Coklat) |
| price_impact | DECIMAL(12,2), DEFAULT 0 | Selisih harga dari harga dasar |
| stock_quantity | INT, DEFAULT 0 | Stok khusus varian |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Aturan bisnis:**
- Harga final = `product.price + variant.price_impact` (atau `discount_price + price_impact`)
- Jika produk punya varian, stok dikelola per varian (bukan dari `products.stock_quantity`)

---

### Tabel: `store_settings`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INT, PK | Selalu bernilai 1 (singleton) |
| store_name | VARCHAR(255) | Nama toko |
| wa_numbers | JSON | Array nomor WA: `["6281234567890", "6289876543210"]` |
| wa_template | TEXT, NULLABLE | Template pesan custom WhatsApp |
| address | TEXT, NULLABLE | Alamat fisik toko |
| social_links | JSON, NULLABLE | `{"instagram": "...", "tiktok": "...", "facebook": "..."}` |
| logo_path | VARCHAR(255), NULLABLE | Path logo toko |
| updated_at | TIMESTAMP | |

**Aturan bisnis:**
- Hanya 1 row di tabel ini (singleton)
- Nomor WA format internasional, dimulai "62", panjang 10-15 digit
- Multiple nomor untuk rotasi otomatis saat kirim pesanan

---

### Tabel: `order_logs`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | BIGINT, PK | Auto increment |
| items_json | JSON | Snapshot pesanan: `[{name, variant, qty, price}]` |
| buyer_info_json | JSON | Data pemesan: `{name, phone, address, notes}` |
| total_amount | DECIMAL(12,2) | Total estimasi harga |
| wa_number_used | VARCHAR(20) | Nomor WA yang digunakan untuk pesanan ini |
| created_at | TIMESTAMP | Waktu pesanan dikirim |

**Aturan bisnis:**
- Data disimpan sebagai JSON snapshot (bukan relasi) — histori tidak berubah saat produk diedit/dihapus
- Bersifat informatif, bukan order management system

---

## Kebutuhan Data

### Data Wajib untuk Operasional

| Data | Kebutuhan Minimum | Keterangan |
|------|-------------------|------------|
| Admin user | 1 akun | Diperlukan untuk mengelola toko |
| Store settings | 1 row | Nama toko + min 1 nomor WA |
| Kategori | Min 1 | Produk wajib punya kategori |
| Produk | Min 1 | Produk dengan min 1 gambar |

### Struktur Session Keranjang

Keranjang disimpan di session (tanpa database), dengan format:

```json
[
  {
    "product_id": 1,
    "variant_id": 3,
    "name": "Kemeja Flanel",
    "variant": "Ukuran L / Warna Hitam",
    "price": 150000,
    "qty": 2,
    "max_stock": 10,
    "image": "products/kemeja-flanel-1.jpg"
  }
]
```

### Format Pesan WhatsApp (Default)

```
🛒 *Pesanan Baru*
Toko: *Nama Toko*

📋 *Detail Pesanan:*
─────────────────
1. Kemeja Flanel (Ukuran L)
   2 x Rp 150.000 = Rp 300.000
2. Celana Jeans
   1 x Rp 250.000 = Rp 250.000
─────────────────
*Total Estimasi: Rp 550.000*

👤 *Data Pemesan:*
Nama: Budi Santoso
No. WA: 081234567890
Alamat: Jl. Merdeka No. 10, Jakarta
Catatan: Kirim sore ya

Terima kasih! 🙏
```

### Template Pesan Custom (Placeholder yang Tersedia)

| Placeholder | Isi |
|-------------|-----|
| `{store_name}` | Nama toko |
| `{order_details}` | Detail pesanan terformat |
| `{total}` | Total estimasi (format Rupiah) |
| `{buyer_name}` | Nama pemesan |
| `{buyer_phone}` | Nomor WA pemesan |
| `{buyer_address}` | Alamat pemesan |
| `{buyer_notes}` | Catatan pemesan |

---

## Alur Pemesanan (End-to-End)

```
1. Pembeli → Buka beranda (/)
2. Pembeli → Jelajahi katalog atau klik kategori
3. Pembeli → Klik produk → Lihat detail
4. Pembeli → Pilih varian (jika ada) → Tambah ke keranjang
5. Pembeli → Buka keranjang (/keranjang)
6. Pembeli → Atur jumlah, isi form data diri
7. Pembeli → Klik "Kirim Pesanan via WhatsApp"
8. Sistem → Validasi form + stok
9. Sistem → Format pesan via WhatsAppMessageBuilder
10. Sistem → Simpan ke order_logs
11. Sistem → Kosongkan keranjang
12. Sistem → Redirect ke api.whatsapp.com dengan pesan ter-encode
13. Pembeli → Konfirmasi pengiriman di WhatsApp
```

---

## Fitur Keamanan

| Fitur | Implementasi |
|-------|--------------|
| Autentikasi | Laravel Breeze + bcrypt hashing |
| Proteksi admin | Middleware `auth` + `AdminMiddleware` |
| CSRF | `@csrf` directive di semua form |
| XSS Prevention | Blade `{{ }}` auto-escape |
| Validasi input | Server-side validation di semua controller |
| Rate limiting | Throttle pada search dan cart operations |
| Session security | httponly, secure, samesite flags |

---

## Cara Menjalankan

```bash
# 1. Clone & install dependencies
composer install
npm install && npm run build

# 2. Konfigurasi environment
cp .env.example .env
# Edit .env: set DB_CONNECTION=pgsql, DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan key:generate

# 3. Setup database
php artisan migrate
php artisan db:seed

# 4. Setup storage link
php artisan storage:link

# 5. Jalankan server
php artisan serve
```

**Akses:**
- Publik: `http://localhost:8000`
- Admin: `http://localhost:8000/login` → masuk dengan akun admin dari seeder
