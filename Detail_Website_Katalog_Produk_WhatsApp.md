# Dokumentasi Spesifikasi Teknis & Desain Sistem
## Website Katalog Produk & Pemesanan WhatsApp

Dokumen ini berisi panduan detail dan komprehensif untuk pembuatan **Website Katalog Produk & Pemesanan WhatsApp** sebagai solusi e-commerce sederhana tanpa kerumitan *payment gateway* dan *checkout system* yang rumit.

---

## 1. Ringkasan Sistem (Executive Summary)

Website Katalog Produk & Pemesanan WhatsApp adalah platform e-commerce ringan yang berfokus pada kemudahan navigasi produk oleh calon pembeli dan kemudahan pengelolaan oleh pemilik usaha. 

### Alasan Penggunaan Konsep Ini:
1. **Mengeliminasi Hambatan Pembayaran:** Pembeli di Indonesia sangat terbiasa bertransaksi via WhatsApp (transfer bank manual, negosiasi, tanya jawab stok).
2. **Biaya Operasional Rendah:** Tidak memerlukan integrasi *payment gateway* berbayar atau biaya komisi per transaksi.
3. **Konversi Tinggi & Efek Urgensi:** Berkomunikasi langsung via WhatsApp membangun kepercayaan yang lebih tinggi, serta penayangan stok riil secara cermat memberikan dorongan transaksi (*FOMO*).

---

## 2. Fitur-Fitur Sistem (Detailed Features)

### A. Sisi Pengguna Publik (Frontend / Buyer)
1. **Halaman Beranda (Landing Page):**
   - Banner promo / slider gambar interaktif.
   - Daftar kategori produk cepat.
   - Produk unggulan (*Featured Products*) & Produk terbaru.
   - Kolom pencarian cepat (*Real-time Search*).
2. **Katalog & Filter Produk:**
   - Filter berdasarkan kategori, rentang harga, ketersediaan stok, dan pengurutan (terbaru, termurah, termahal).
   - Tampilan grid yang responsif dan cepat di perangkat *mobile*.
3. **Halaman Detail Produk & Indikator Stok:**
   - Galeri foto produk (multiple image).
   - Informasi harga, harga diskon (coret), dan **tampilan sisa stok riil** (misal: "Stok Tersedia: 15 pcs" atau peringatan "Sisa 2 pcs lagi!").
   - Pilihan varian produk (misal: Ukuran, Warna, Rasa) beserta indikator stok per varian.
   - Deskripsi lengkap produk.
   - Tombol cepat *"Beli Langsung via WA"* dan *"Tambah ke Keranjang"*.
   - **Logika Stok Habis:** Jika stok 0, tombol order otomatis mati (*disabled*) dan gambar produk diberi badge *"Stok Habis"*.
4. **Keranjang Belanja Sederhana (Cart):**
   - Daftar barang yang dipilih beserta varian, jumlah, dan subtotal harga.
   - Batasan maksimal jumlah order (*Cart Limit*) sesuai ketersediaan sisa stok produk/varian.
   - Fitur ubah jumlah (*quantity*) atau hapus item.
   - Form data pemesan singkat: Nama, Nomor WA, Alamat Pengiriman, dan Catatan Tambahan.
   - Tombol *"Kirim Pesanan via WhatsApp"* yang otomatis mereset keranjang setelah berhasil dikirim.
5. **Navigasi & Kontak:**
   - Tombol melayang (*Floating Widget*) WhatsApp untuk layanan pelanggan (CS).
   - Tautan ke media sosial resmi (Instagram, TikTok, Facebook).

### B. Sisi Pengelola (Backend / Admin Dashboard)
1. **Manajemen Produk & Update Stok:**
   - Tambah, edit, hapus produk (CRUD).
   - Upload multiple gambar produk.
   - Pengelolaan varian produk beserta jumlah stok masing-masing varian.
   - Pengaturan harga dasar, harga promo, serta **sisa jumlah stok (`stock_quantity`)**.
   - Fitur tombol cepat (+ / -) di tabel admin untuk *quick update* stok secara manual setelah ada transaksi masuk di WA.
2. **Manajemen Kategori:**
   - Tambah, edit, dan susun urutan kategori produk.
3. **Pengaturan Toko & WhatsApp:**
   - Input nomor WhatsApp penerima pesanan (support multiple nomor CS dengan rotasi otomatis jika diperlukan).
   - Custom template pesan WhatsApp (pengaturan format teks yang dikirim ke penjual).
   - Pengaturan profil toko (Nama toko, logo, alamat dasar, link sosmed).
4. **Notifikasi & Rekap Pesanan:**
   - Peringatan stok rendah di *dashboard admin* jika ada produk/varian dengan stok ≤ 2 pcs.
   - Histori pemesanan yang pernah di-klik oleh pembeli sebagai log internal toko untuk melihat tren produk terlaris.

---

## 3. Perancangan Database (Database Schema)

Berikut adalah struktur tabel relational database (misal menggunakan MySQL / PostgreSQL) untuk menjalankan aplikasi ini secara optimal, dilengkapi kolom jumlah stok riil.

### 1. Tabel `users` (Admin/Pengelola)
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK, Auto Increment) | Primary Key |
| `name` | VARCHAR(255) | Nama Admin |
| `email` | VARCHAR(255) (Unique) | Email untuk Login |
| `password` | VARCHAR(255) | Hashed Password |
| `created_at` | TIMESTAMP | Tanggal dibuat |

### 2. Tabel `categories` (Kategori)
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK, Auto Increment) | Primary Key |
| `name` | VARCHAR(255) | Nama Kategori (misal: Pakaian Pria) |
| `slug` | VARCHAR(255) (Unique) | URL friendly (misal: pakaian-pria) |
| `image` | VARCHAR(255) (Nullable) | Foto Ikon Kategori |
| `created_at` | TIMESTAMP | Tanggal dibuat |

### 3. Tabel `products` (Produk)
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK, Auto Increment) | Primary Key |
| `category_id` | BIGINT (FK) | Relasi ke `categories.id` |
| `name` | VARCHAR(255) | Nama Produk |
| `slug` | VARCHAR(255) (Unique) | URL friendly produk |
| `description` | TEXT | Deskripsi Produk |
| `price` | DECIMAL(12,2) | Harga Asli |
| `discount_price`| DECIMAL(12,2) (Nullable) | Harga Diskon |
| `stock_quantity`| INT (Default: 0) | Sisa jumlah stok produk |
| `is_unlimited`  | BOOLEAN (Default: false) | True jika produk non-fisik / stok unlimited |
| `is_featured` | BOOLEAN (Default: false)| Tampil di Produk Unggulan |
| `created_at` | TIMESTAMP | Tanggal dibuat |

### 4. Tabel `product_images` (Gambar Produk)
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK, Auto Increment) | Primary Key |
| `product_id` | BIGINT (FK) | Relasi ke `products.id` |
| `image_path` | VARCHAR(255) | Path/URL Foto |
| `is_primary` | BOOLEAN (Default: false)| Foto Utama Katalog |

### 5. Tabel `product_variants` (Varian Produk)
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK, Auto Increment) | Primary Key |
| `product_id` | BIGINT (FK) | Relasi ke `products.id` |
| `variant_name` | VARCHAR(100) | Tipe Varian (misal: Warna / Ukuran) |
| `variant_value`| VARCHAR(100) | Nilai Varian (misal: Merah / XL) |
| `price_impact` | DECIMAL(12,2) (Default: 0)| Tambahan harga varian |
| `stock_quantity`| INT (Default: 0) | Sisa jumlah stok khusus varian ini |

### 6. Tabel `store_settings` (Pengaturan Toko)
| Field | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | INT (PK) | Primary Key |
| `store_name` | VARCHAR(255) | Nama Toko |
| `wa_number` | VARCHAR(20) | Nomor WA Penjual (format: 628xxx) |
| `wa_template` | TEXT | Format pesan bawaan WA |
| `address` | TEXT | Alamat Toko |

---

## 4. Alur Kerja & Proses (System Flow)

### A. Alur Pemesanan & Pengelolaan Stok (Buyer & Seller Journey)

```
[Pembeli Membuka Website] 
       │
       ▼
[Menjelajahi Katalog & Cek Sisa Stok]
       │
       ▼
[Memilih Produk / Varian (Sistem Batasi Qty Sesuai Stok)]
       │
       ├──────────────────────────────────────────┐
       ▼                                          ▼
[Klik "Beli Langsung"]                  [Klik "Tambah ke Keranjang"]
       │                                          │
       │                                          ▼
       │                                [Membuka Halaman Keranjang]
       │                                          │
       │                                [Atur Qty & Isi Data Pemesan]
       │                                          │
       └───────────────────┬──────────────────────┘
                           │
                           ▼
            [Klik "Kirim Pesanan via WA"]
                           │
                           ▼
     [Redirect ke WhatsApp & Pesan Terkirim]
                           │
                           ▼
      [Penjual Verifikasi Pembayaran via WA]
                           │
                           ▼
  [Penjual Update/Kurangi Stok via Dashboard Admin]
```

---

### B. Detail Format Pesan WhatsApp (Otomatis Ter-generate)

Ketika pembeli menekan tombol **"Kirim Pesanan via WhatsApp"**, sistem akan membuka URL dengan sintaks:
`https://api.whatsapp.com/send?phone=6281234567890&text=ENCODED_TEXT`

#### Contoh Teks yang Dihasilkan (Format Rapi):

```text
Halo *[Nama Toko]*, Saya ingin memesan produk berikut:

*DETAIL PESANAN:*
1. Kemeja Flanel Polos
   - Varian: Ukuran L / Warna Hitam
   - Qty: 2 pcs
   - Harga: Rp 150.000 (x2 = Rp 300.000)

2. Celana Chino Cream
   - Varian: Ukuran 32
   - Qty: 1 pcs
   - Harga: Rp 200.000

----------------------------------
*TOTAL ESTIMASI:* Rp 500.000
*(Belum termasuk ongkos kirim)*

*DATA PEMESAN:*
- Nama: Budi Santoso
- No. WA: 081298765432
- Alamat Kirim: Jl. Mawar No. 12, Kec. Serpong, Kota Tangerang Selatan
- Catatan: Tolong dibungkus bubble wrap tebal.

Apakah stok produk di atas masih tersedia dan berapa total ongkirnya? Terima kasih!
```

---

## 5. Panduan Pengembangan (Tech Stack — Laravel Full Stack)

Aplikasi ini dibangun menggunakan **Laravel Full Stack** dengan Blade + Livewire sebagai pendekatan monolitik yang produktif, tanpa perlu memisahkan frontend dan backend.

### Tech Stack:

| Layer | Teknologi |
|-------|-----------|
| **Framework** | Laravel 11 |
| **Templating** | Blade Templates |
| **Interaktivitas** | Livewire 3 (keranjang belanja, filter produk, real-time search) |
| **Styling** | Tailwind CSS 3 |
| **Database** | PostgreSQL |
| **File Storage** | Laravel Storage (local disk / S3) |
| **Auth** | Laravel Breeze (admin panel) |
| **Deployment** | VPS (Nginx + PHP-FPM) / Shared Hosting |

---

### Struktur Direktori Utama (Laravel):

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── VariantController.php
│   │   │   └── StoreSettingController.php
│   │   └── Public/
│   │       ├── HomeController.php
│   │       ├── CatalogController.php
│   │       ├── ProductDetailController.php
│   │       └── CartController.php
│   ├── Livewire/
│   │   ├── CartManager.php          (state keranjang + kirim WA)
│   │   ├── ProductFilter.php        (filter & search real-time)
│   │   └── StockUpdater.php         (quick stock +/- di admin)
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Product.php
│   ├── ProductImage.php
│   ├── ProductVariant.php
│   ├── StoreSetting.php
│   └── OrderLog.php
├── Services/
│   └── WhatsAppMessageBuilder.php   (generate URL + format teks)
└── ...

resources/views/
├── layouts/
│   ├── app.blade.php               (layout publik)
│   └── admin.blade.php             (layout admin)
├── public/
│   ├── home.blade.php
│   ├── catalog.blade.php
│   ├── product-detail.blade.php
│   └── cart.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── products/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── categories/
│   └── settings.blade.php
└── livewire/
    ├── cart-manager.blade.php
    ├── product-filter.blade.php
    └── stock-updater.blade.php

database/migrations/
├── create_users_table.php
├── create_categories_table.php
├── create_products_table.php
├── create_product_images_table.php
├── create_product_variants_table.php
├── create_store_settings_table.php
└── create_order_logs_table.php

routes/
├── web.php                          (route publik)
└── admin.php                        (route admin, middleware auth)
```

---

### Contoh Routing (`routes/web.php`):

```php
<?php
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\ProductDetailController;
use App\Http\Controllers\Public\CartController;

// Halaman Publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/katalog/{category:slug}', [CatalogController::class, 'byCategory'])->name('catalog.category');
Route::get('/produk/{product:slug}', [ProductDetailController::class, 'show'])->name('product.detail');
Route::get('/keranjang', [CartController::class, 'index'])->name('cart');
```

### Contoh Routing (`routes/admin.php`):

```php
<?php
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\StoreSettingController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::get('settings', [StoreSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [StoreSettingController::class, 'update'])->name('settings.update');
});
```

---

### Contoh Livewire: Cart Manager (`app/Http/Livewire/CartManager.php`):

```php
<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\StoreSetting;
use App\Services\WhatsAppMessageBuilder;

class CartManager extends Component
{
    public array $cart = [];
    public string $name = '';
    public string $phone = '';
    public string $address = '';
    public string $notes = '';

    public function mount()
    {
        $this->cart = session('cart', []);
    }

    public function addItem(int $productId, ?int $variantId = null, int $qty = 1)
    {
        // Validasi stok sebelum tambah
        // Simpan ke session
        session(['cart' => $this->cart]);
    }

    public function updateQty(int $index, int $qty)
    {
        // Update quantity dengan batasan stok
        session(['cart' => $this->cart]);
    }

    public function removeItem(int $index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        session(['cart' => $this->cart]);
    }

    public function sendToWhatsApp()
    {
        $this->validate([
            'name' => 'required|min:3',
            'phone' => 'required|min:10',
            'address' => 'required|min:10',
        ]);

        $settings = StoreSetting::first();
        $url = WhatsAppMessageBuilder::build(
            phone: $settings->wa_number,
            storeName: $settings->store_name,
            cart: $this->cart,
            buyer: [
                'name' => $this->name,
                'phone' => $this->phone,
                'address' => $this->address,
                'notes' => $this->notes,
            ]
        );

        // Log pesanan
        OrderLog::create([...]);

        // Reset cart
        session()->forget('cart');
        $this->cart = [];

        return redirect()->away($url);
    }

    public function render()
    {
        return view('livewire.cart-manager');
    }
}
```

---

### Contoh Service: WhatsApp Message Builder (`app/Services/WhatsAppMessageBuilder.php`):

```php
<?php
namespace App\Services;

class WhatsAppMessageBuilder
{
    public static function build(string $phone, string $storeName, array $cart, array $buyer): string
    {
        $lines = ["Halo *{$storeName}*, Saya ingin memesan produk berikut:\n", "*DETAIL PESANAN:*"];
        $total = 0;

        foreach ($cart as $i => $item) {
            $no = $i + 1;
            $subtotal = $item['price'] * $item['qty'];
            $total += $subtotal;

            $lines[] = "{$no}. {$item['name']}";
            if (!empty($item['variant'])) {
                $lines[] = "   - Varian: {$item['variant']}";
            }
            $lines[] = "   - Qty: {$item['qty']} pcs";
            $lines[] = "   - Harga: Rp " . number_format($subtotal, 0, ',', '.') . "\n";
        }

        $lines[] = "----------------------------------";
        $lines[] = "*TOTAL ESTIMASI:* Rp " . number_format($total, 0, ',', '.');
        $lines[] = "*(Belum termasuk ongkos kirim)*\n";
        $lines[] = "*DATA PEMESAN:*";
        $lines[] = "- Nama: {$buyer['name']}";
        $lines[] = "- No. WA: {$buyer['phone']}";
        $lines[] = "- Alamat Kirim: {$buyer['address']}";

        if (!empty($buyer['notes'])) {
            $lines[] = "- Catatan: {$buyer['notes']}";
        }

        $lines[] = "\nApakah stok produk di atas masih tersedia dan berapa total ongkirnya? Terima kasih!";

        $text = urlencode(implode("\n", $lines));

        return "https://api.whatsapp.com/send?phone={$phone}&text={$text}";
    }
}
```

---

### Perintah Instalasi & Setup:

```bash
# 1. Buat project Laravel
composer create-project laravel/laravel katalog-wa

# 2. Install dependensi
cd katalog-wa
composer require livewire/livewire
composer require laravel/breeze --dev
php artisan breeze:install blade

# 3. Install Tailwind (sudah termasuk di Breeze)
npm install
npm run build

# 4. Setup database (.env)
# DB_CONNECTION=mysql
# DB_DATABASE=katalog_wa
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Jalankan migration & seeder
php artisan migrate
php artisan db:seed

# 6. Jalankan server
php artisan serve
```

---

### Deployment (VPS / Shared Hosting):

| Langkah | Perintah / Aksi |
|---------|-----------------|
| Push ke GitHub | `git push origin main` |
| SSH ke server | `ssh user@server` |
| Clone repo | `git clone <repo-url>` |
| Install deps | `composer install --optimize-autoloader --no-dev` |
| Set .env | Copy `.env.example`, isi credentials |
| Generate key | `php artisan key:generate` |
| Migrate | `php artisan migrate --force` |
| Build assets | `npm run build` |
| Symlink storage | `php artisan storage:link` |
| Set permissions | `chmod -R 775 storage bootstrap/cache` |
| Nginx config | Point root ke `public/` |

