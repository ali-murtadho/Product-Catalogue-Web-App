# Dokumen Requirements

## Pendahuluan

Website Katalog Produk & Pemesanan WhatsApp adalah platform e-commerce ringan yang memungkinkan calon pembeli menjelajahi katalog produk secara mudah dan melakukan pemesanan langsung melalui WhatsApp. Sistem ini mengeliminasi kerumitan payment gateway dengan memanfaatkan WhatsApp sebagai kanal komunikasi pemesanan. Platform ini dibangun menggunakan Laravel 11, Blade Templates, Livewire 3, Tailwind CSS 3, dan PostgreSQL.

## Glossary

- **Sistem**: Aplikasi web katalog produk dan pemesanan WhatsApp secara keseluruhan
- **Pembeli**: Pengguna publik yang mengakses website untuk menjelajahi produk dan melakukan pemesanan
- **Admin**: Pengelola toko yang memiliki akses ke dashboard administrasi
- **Katalog**: Kumpulan produk yang ditampilkan secara publik kepada pembeli
- **Keranjang**: Penyimpanan sementara berbasis session untuk item yang dipilih pembeli sebelum dikirim ke WhatsApp
- **Varian**: Variasi produk berdasarkan atribut tertentu seperti ukuran, warna, atau rasa
- **Stok**: Jumlah ketersediaan produk atau varian produk yang dapat dipesan
- **Order_Log**: Catatan aktivitas pemesanan yang tercatat saat pembeli mengklik tombol kirim ke WhatsApp
- **WhatsApp_Message_Builder**: Service yang memformat data pesanan menjadi teks terstruktur untuk dikirim via WhatsApp
- **Store_Settings**: Konfigurasi toko yang mencakup nomor WhatsApp, template pesan, dan profil toko
- **Slug**: Format URL yang ramah mesin pencari menggunakan huruf kecil dan tanda hubung

## Requirements

### Requirement 1: Halaman Beranda

**User Story:** Sebagai pembeli, saya ingin melihat halaman beranda yang informatif, sehingga saya dapat dengan cepat menemukan produk yang menarik dan kategori yang tersedia.

#### Acceptance Criteria

1. WHEN pembeli mengakses halaman beranda, THE Sistem SHALL menampilkan banner promo dalam format slider gambar interaktif
2. WHEN halaman beranda dimuat, THE Sistem SHALL menampilkan daftar kategori produk dengan gambar ikon masing-masing
3. WHEN halaman beranda dimuat, THE Sistem SHALL menampilkan daftar produk unggulan (is_featured = true) dengan gambar, nama, dan harga
4. WHEN halaman beranda dimuat, THE Sistem SHALL menampilkan daftar produk terbaru berdasarkan tanggal pembuatan secara descending
5. WHEN pembeli mengetik di kolom pencarian cepat, THE Sistem SHALL menampilkan hasil pencarian produk secara real-time tanpa memuat ulang halaman

### Requirement 2: Katalog dan Filter Produk

**User Story:** Sebagai pembeli, saya ingin menjelajahi katalog produk dengan filter yang fleksibel, sehingga saya dapat menemukan produk yang sesuai kebutuhan dengan cepat.

#### Acceptance Criteria

1. WHEN pembeli mengakses halaman katalog, THE Sistem SHALL menampilkan semua produk aktif dalam format grid yang responsif
2. WHEN pembeli memilih filter kategori, THE Sistem SHALL menampilkan hanya produk yang termasuk dalam kategori tersebut
3. WHEN pembeli mengatur filter rentang harga, THE Sistem SHALL menampilkan hanya produk dengan harga dalam rentang yang ditentukan
4. WHEN pembeli memilih filter ketersediaan stok, THE Sistem SHALL menampilkan hanya produk yang memiliki stok lebih dari 0 atau berstatus is_unlimited = true
5. WHEN pembeli memilih opsi pengurutan, THE Sistem SHALL mengurutkan produk sesuai kriteria yang dipilih (terbaru, termurah, termahal)
6. WHEN pembeli menerapkan filter, THE Sistem SHALL memperbarui tampilan katalog secara real-time tanpa memuat ulang halaman penuh

### Requirement 3: Halaman Detail Produk

**User Story:** Sebagai pembeli, saya ingin melihat informasi lengkap produk termasuk stok dan varian, sehingga saya dapat membuat keputusan pembelian yang tepat.

#### Acceptance Criteria

1. WHEN pembeli membuka halaman detail produk, THE Sistem SHALL menampilkan galeri foto produk dengan kemampuan navigasi antar gambar
2. WHEN produk memiliki harga diskon (discount_price tidak null), THE Sistem SHALL menampilkan harga asli dengan format coret dan harga diskon sebagai harga aktif
3. WHEN produk memiliki stok terbatas (is_unlimited = false), THE Sistem SHALL menampilkan indikator sisa stok riil dalam format jumlah pcs
4. WHILE stok produk atau varian bernilai kurang dari atau sama dengan 2, THE Sistem SHALL menampilkan peringatan visual "Sisa X pcs lagi!" dengan warna mencolok
5. WHEN produk memiliki varian, THE Sistem SHALL menampilkan opsi pemilihan varian beserta indikator stok per varian
6. WHEN pembeli memilih varian tertentu, THE Sistem SHALL memperbarui tampilan harga sesuai price_impact varian dan menampilkan stok spesifik varian tersebut
7. WHEN stok produk atau varian yang dipilih bernilai 0 dan is_unlimited = false, THE Sistem SHALL menonaktifkan tombol pemesanan dan menampilkan badge "Stok Habis" pada gambar produk

### Requirement 4: Keranjang Belanja

**User Story:** Sebagai pembeli, saya ingin mengumpulkan beberapa produk dalam keranjang sebelum memesan sekaligus via WhatsApp, sehingga saya dapat memesan banyak produk dalam satu pesan.

#### Acceptance Criteria

1. WHEN pembeli menambahkan produk ke keranjang, THE Sistem SHALL menyimpan item ke session dengan informasi produk, varian yang dipilih, kuantitas, dan harga
2. WHEN pembeli mengubah kuantitas item di keranjang, THE Sistem SHALL memvalidasi bahwa kuantitas tidak melebihi stok tersedia untuk produk atau varian tersebut
3. IF pembeli mencoba menambahkan kuantitas melebihi stok tersedia, THEN THE Sistem SHALL menampilkan pesan error dan membatasi kuantitas pada nilai stok maksimum
4. WHEN pembeli menghapus item dari keranjang, THE Sistem SHALL menghapus item tersebut dan memperbarui total harga secara real-time
5. THE Sistem SHALL menampilkan subtotal harga per item dan total keseluruhan harga di halaman keranjang
6. WHEN pembeli mengisi form data pemesan, THE Sistem SHALL memvalidasi bahwa nama minimal 3 karakter, nomor WhatsApp minimal 10 digit, dan alamat pengiriman minimal 10 karakter

### Requirement 5: Pemesanan via WhatsApp

**User Story:** Sebagai pembeli, saya ingin mengirim pesanan saya langsung ke WhatsApp penjual dengan format pesan yang rapi, sehingga proses pemesanan menjadi cepat dan mudah.

#### Acceptance Criteria

1. WHEN pembeli mengklik tombol "Kirim Pesanan via WhatsApp", THE WhatsApp_Message_Builder SHALL memformat data pesanan menjadi teks terstruktur yang mencakup detail produk, kuantitas, harga, total estimasi, dan data pemesan
2. WHEN pesan WhatsApp telah di-generate, THE Sistem SHALL mengarahkan pembeli ke URL WhatsApp API dengan nomor tujuan dari Store_Settings dan teks pesan yang telah di-encode
3. WHEN pesanan berhasil dikirim ke WhatsApp, THE Sistem SHALL mencatat data pesanan ke Order_Log dengan timestamp, detail item, dan informasi pemesan
4. WHEN pesanan berhasil dikirim ke WhatsApp, THE Sistem SHALL mengosongkan keranjang dan mereset session
5. WHEN Store_Settings memiliki multiple nomor WhatsApp, THE Sistem SHALL memilih nomor tujuan secara bergiliran (rotasi)

### Requirement 6: Widget WhatsApp dan Navigasi Kontak

**User Story:** Sebagai pembeli, saya ingin dapat menghubungi customer service dengan mudah dari halaman manapun, sehingga saya dapat bertanya tentang produk atau pesanan.

#### Acceptance Criteria

1. THE Sistem SHALL menampilkan tombol WhatsApp melayang (floating widget) yang terlihat di semua halaman publik
2. WHEN pembeli mengklik floating widget WhatsApp, THE Sistem SHALL membuka WhatsApp ke nomor CS yang dikonfigurasi di Store_Settings
3. THE Sistem SHALL menampilkan tautan media sosial resmi toko (Instagram, TikTok, Facebook) di footer semua halaman publik

### Requirement 7: Dashboard Admin

**User Story:** Sebagai admin, saya ingin melihat ringkasan kondisi toko dan peringatan stok rendah, sehingga saya dapat mengambil tindakan dengan cepat.

#### Acceptance Criteria

1. WHEN admin mengakses dashboard, THE Sistem SHALL menampilkan daftar produk dan varian yang memiliki stok kurang dari atau sama dengan 2 pcs sebagai peringatan stok rendah
2. WHEN admin mengakses dashboard, THE Sistem SHALL menampilkan histori Order_Log dengan informasi tanggal, item yang dipesan, dan data pemesan
3. WHILE admin belum melakukan autentikasi, THE Sistem SHALL menolak akses ke semua halaman admin dan mengarahkan ke halaman login

### Requirement 8: Manajemen Produk (Admin)

**User Story:** Sebagai admin, saya ingin mengelola data produk secara lengkap termasuk gambar dan varian, sehingga katalog produk selalu up-to-date.

#### Acceptance Criteria

1. WHEN admin membuat produk baru, THE Sistem SHALL memvalidasi dan menyimpan data produk termasuk nama, slug, deskripsi, harga, kategori, stok, dan status featured
2. WHEN admin mengunggah gambar produk, THE Sistem SHALL menyimpan multiple gambar ke Laravel Storage dan menandai satu gambar sebagai gambar utama (is_primary = true)
3. WHEN admin menambahkan varian produk, THE Sistem SHALL menyimpan data varian termasuk nama varian, nilai varian, dampak harga (price_impact), dan stok per varian
4. WHEN admin mengedit produk, THE Sistem SHALL memperbarui data produk dan menampilkan perubahan secara langsung di katalog publik
5. WHEN admin menghapus produk, THE Sistem SHALL menghapus produk beserta gambar dan varian terkait dari database dan storage
6. WHEN admin menggunakan tombol quick stock (+/-), THE Sistem SHALL memperbarui jumlah stok produk atau varian secara instan tanpa memuat ulang halaman
7. THE Sistem SHALL menghasilkan slug secara otomatis dari nama produk menggunakan format huruf kecil dan tanda hubung

### Requirement 9: Manajemen Kategori (Admin)

**User Story:** Sebagai admin, saya ingin mengelola kategori produk dan mengatur urutannya, sehingga pembeli dapat menavigasi katalog dengan terstruktur.

#### Acceptance Criteria

1. WHEN admin membuat kategori baru, THE Sistem SHALL memvalidasi dan menyimpan data kategori termasuk nama, slug, dan gambar ikon
2. WHEN admin mengedit kategori, THE Sistem SHALL memperbarui data kategori dan semua tampilan terkait di halaman publik
3. WHEN admin mengatur urutan kategori, THE Sistem SHALL menyimpan urutan tampil dan menampilkan kategori sesuai urutan yang ditentukan
4. WHEN admin menghapus kategori, THE Sistem SHALL mencegah penghapusan jika masih terdapat produk yang terkait dengan kategori tersebut

### Requirement 10: Pengaturan Toko (Admin)

**User Story:** Sebagai admin, saya ingin mengkonfigurasi informasi toko dan template pesan WhatsApp, sehingga komunikasi dengan pembeli berjalan profesional.

#### Acceptance Criteria

1. WHEN admin memperbarui pengaturan toko, THE Sistem SHALL menyimpan nama toko, nomor WhatsApp penerima pesanan, template pesan WhatsApp, alamat toko, dan tautan media sosial
2. WHEN admin mengkonfigurasi template pesan WhatsApp, THE Sistem SHALL menggunakan template tersebut sebagai format dasar saat WhatsApp_Message_Builder memformat pesan pesanan
3. WHEN admin memasukkan nomor WhatsApp, THE Sistem SHALL memvalidasi format nomor dalam format internasional (dimulai dengan 62)

### Requirement 11: SEO dan Performa

**User Story:** Sebagai admin, saya ingin website memiliki URL yang ramah mesin pencari dan performa yang cepat, sehingga website mudah ditemukan dan nyaman digunakan pembeli.

#### Acceptance Criteria

1. THE Sistem SHALL menggunakan slug sebagai URL untuk halaman produk dan kategori dalam format /produk/{slug} dan /katalog/{slug}
2. THE Sistem SHALL menerapkan lazy loading pada gambar produk di halaman katalog dan beranda untuk mempercepat waktu muat
3. THE Sistem SHALL mengoptimasi gambar yang diunggah dengan kompresi otomatis dan membatasi ukuran file maksimum 2MB per gambar
4. THE Sistem SHALL menyediakan meta tags (title, description, og:image) yang relevan pada setiap halaman produk untuk optimasi mesin pencari

### Requirement 12: Keamanan

**User Story:** Sebagai admin, saya ingin sistem terlindungi dari akses tidak sah dan serangan umum, sehingga data toko dan pembeli tetap aman.

#### Acceptance Criteria

1. THE Sistem SHALL menggunakan Laravel Breeze untuk autentikasi admin dengan hashing password menggunakan bcrypt
2. THE Sistem SHALL menerapkan validasi input pada semua form publik dan admin untuk mencegah data yang tidak valid
3. THE Sistem SHALL menerapkan proteksi XSS dengan escape otomatis pada semua output Blade template
4. THE Sistem SHALL menerapkan CSRF protection pada semua form submission
5. IF pengguna yang tidak terautentikasi mengakses route admin, THEN THE Sistem SHALL mengarahkan pengguna tersebut ke halaman login
