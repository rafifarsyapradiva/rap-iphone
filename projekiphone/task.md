# WORK BREAKDOWN STRUCTURE (WBS) & TASK ROADMAP
## Platform E-Commerce Jual Beli iPhone (Rap-iPhone)

---

### Phase 1: Inisialisasi Proyek & Konfigurasi Environment
- [x] **1.1. Setup Project Laravel 12 & Database**
  - [x] Inisialisasi proyek Laravel 12 di direktori kerja (`rap-iphone`).
  - [x] Konfigurasi file `.env` untuk koneksi MySQL phpMyAdmin (`DB_DATABASE=rap_iphone`, `DB_USERNAME=root`, `DB_PASSWORD=`).
  - [x] Konfigurasi Git / struktur repositori dan ignores.
- [x] **1.2. Instalasi Paket Dependensi Kunci**
  - [x] `composer require laravel/socialite` (Autentikasi Google Console).
  - [x] `composer require midtrans/midtrans-php` (Payment Gateway Midtrans).
  - [x] `composer require barryvdh/laravel-dompdf` (Generator Nota PDF).
  - [x] `composer require guzzlehttp/guzzle` (Klien HTTP untuk API WhatsApp Fonnte).
- [x] **1.3. Konfigurasi Variabel Lingkungan Pihak Ketiga**
  - [x] Setup kredensial Google OAuth (`GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`).
  - [x] Setup kredensial Midtrans (`MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION=false`).
  - [x] Setup kredensial Fonnte WhatsApp (`FONNTE_TOKEN`).
  - [x] Setup konfigurasi SMTP Email (`MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, dll).

---

### Phase 2: Skema Basis Data, Migrasi, & Seeder
- [x] **2.1. Pembuatan File Migrasi Tabel**
  - [x] `create_users_table`: Kolom `role` ('customer','admin','owner'), `google_id`, `phone_number`, `address`, `city`.
  - [x] `create_categories_table` & `create_products_table`: Seri iPhone, kondisi (new/second), garansi, grade, deskripsi.
  - [x] `create_product_variants_table`: Kapasitas penyimpanan, nama & heksadesimal warna, harga pokok (HPP), harga jual, stok, nomor IMEI, persentase baterai.
  - [x] `create_product_images_table`: Gambar resolusi tinggi per varian warna.
  - [x] `create_orders_table` & `create_order_items_table`: Nomor nota pesanan, status pesanan, rincian produk, kurir, nomor resi.
  - [x] `create_payments_table`: Snap token, transaction status Midtrans, payment type, raw payload webhook.
  - [x] `create_trade_ins_table`: Form data jual/tukar tambah, kondisi fisik & fungsional, estimasi harga, harga deal, status.
  - [x] `create_notifications_log_table`: Riwayat log pengiriman WhatsApp Fonnte dan Email SMTP.
- [x] **2.2. Pembuatan Model Eloquent & Relasi**
  - [x] Model `User`, `Category`, `Product`, `ProductVariant`, `ProductImage`, `Order`, `OrderItem`, `Payment`, `TradeIn`.
  - [x] Relasi: `Product hasMany ProductVariant`, `Order hasMany OrderItem`, `Order hasOne Payment`, `User hasMany Order`.
- [x] **2.3. Seeder Data Awal Realistis (Standar iBox)**
  - [x] User Seeder: Akun Owner (`owner@rap-iphone.com`), Admin (`admin@rap-iphone.com`), Customer demo.
  - [x] Product & Variant Seeder: Seri iPhone 16 Pro, iPhone 16, iPhone 15 Pro, iPhone 14, dan iPhone 13 (Lengkap varian warna resmi Apple & kapasitas 128GB - 1TB).

---

### Phase 3: Desain Sistem & Template Blade Glassmorphism
- [x] **3.1. Pembuatan Core CSS Glassmorphism**
  - [x] Desain token CSS (variabel warna titanium, blur filter, border kaca, glow gradient).
  - [x] Utilitas kartu kaca frosted (`.glass-card`, `.glass-navbar`, `.glass-modal`, `.glass-button`).
- [x] **3.2. Layout Induk (Master Blade Layouts)**
  - [x] Layout Customer (`layouts.customer`): Navbar frosted glass sticky, search modal, badge keranjang, footer profesional.
  - [x] Layout Admin / Owner (`layouts.admin`): Sidebar kaca responsif, header profil, breadcrumbs.
- [x] **3.3. Komponen UI Blade Reusable**
  - [x] Komponen tombol kaca (`<x-glass-button>`), kartu produk (`<x-product-card>`), badge status pesanan (`<x-order-badge>`), indikator kesehatan baterai (`<x-battery-meter>`).


---

### Phase 4: Autentikasi, Google OAuth & Role Middleware
- [x] **4.1. Autentikasi Konvensional & Register**
  - [x] Controller login, register, logout, serta hashing password aman Bcrypt.
- [x] **4.2. Integrasi Google Console Login (Socialite)**
  - [x] `GoogleAuthController`: Redirect ke Google OAuth & Callback handler.
  - [x] Logika sinkronisasi akun baru / existing user otomatis dengan role `customer`.
- [x] **4.3. Middleware Hak Akses (Role Guards)**
  - [x] Implementasi `RoleMiddleware` (`role:admin`, `role:owner`, `role:customer`).
  - [x] Pengalihan rute otomatis sesuai hak akses setelah login.

---

### Phase 5: Katalog Produk & Pengalaman Belanja Customer
- [x] **5.1. Beranda Interaktif (Home Page)**
  - [x] Hero showcase Apple flagship dengan efek floating unit dan ambient glow.
  - [x] Kategori seri iPhone (iPhone 16 Series, iPhone 15 Series, dll).
  - [x] Bagian Unit Baru Garansi Resmi vs Second Like New Teruji.
- [x] **5.2. Halaman Rincian Produk (Product Detail Page - PDP)**
  - [x] Galeri gambar interaktif.
  - [x] Selector warna dinamis (Color dots Apple) yang mengubah gambar aktif.
  - [x] Selector kapasitas penyimpanan (128GB, 256GB, 512GB, 1TB) dengan pembaruan harga instan (*live price update*).
  - [x] Widget informasi kesehatan baterai (*Battery Health*) dan IMEI transparan untuk unit second.
  - [x] Tombol aksi cepat: *"Beli Sekarang"* & *"Tambah ke Keranjang"*.
- [x] **5.3. Modul Keranjang Belanja (Cart System)**
  - [x] Tambah unit ke keranjang dengan varian spesifik.
  - [x] Update kuantitas dan hapus item dengan feedback visual halus.

---

### Phase 6: Checkout Terpadu & Integrasi Midtrans Snap
- [x] **6.1. Halaman Checkout Ringkas (Distraction-Free)**
  - [x] Form alamat penerima dan nomor WhatsApp aktif.
  - [x] Ringkasan rincian pesanan dan total tagihan.
- [x] **6.2. Layanan Pembayaran Midtrans (`MidtransService`)**
  - [x] Konfigurasi SDK Midtrans Snap dengan server key.
  - [x] Generate Snap Token berdasarkan data order unik.
- [x] **6.3. Integrasi Frontend Midtrans Snap Popup**
  - [x] Trigger modal popup pembayaran Midtrans saat checkout disubmit.
  - [x] Penanganan respon frontend (`onSuccess`, `onPending`, `onError`, `onClose`).
- [x] **6.4. Midtrans Webhook Callback Handler (`/api/midtrans/callback`)**
  - [x] Verifikasi keamanan SHA512 signature hash.
  - [x] Pembaruan status pesanan (`settlement` -> `paid`, `cancel` -> `cancelled`).
  - [x] Database transaction: Pengurangan kuantitas stok varian saat status `paid`.
  - [x] Idempotency: Menghindari pemrosesan duplikat untuk webhook yang sama.

---

### Phase 7: Modul Jual & Tukar Tambah iPhone (Trade-In Program)
- [x] **7.1. Wizard Interaktif Pelanggan (Trade-In Wizard)**
  - [x] Form 4 langkah: Pilih Model -> Kapasitas -> Kondisi Fisik & Fungsi -> Estimasi Nilai.
  - [x] Kalkulator penaksir nilai otomatis berbasis parameter kondisi unit.
  - [x] Pilihan pencairan dana: Transfer bank rekening customer atau kupon potongan belanja.
- [x] **7.2. Panel Verifikasi & Inspeksi Admin**
  - [x] Antrean pengajuan trade-in customer.
  - [x] Form inspeksi teknisi toko saat unit diterima.
  - [x] Fitur penyesuaian penawaran harga final dan tombol persetujuan (*Approve / Reject*).

---

### Phase 8: Otomasi Notifikasi WhatsApp (Fonnte) & Email (SMTP)
- [x] **8.1. Layanan Notifikasi WhatsApp (`FonnteService`)**
  - [x] Klien HTTP untuk pengiriman pesan ke API Fonnte.
  - [x] Template pesan: Tagihan Baru, Pembayaran Lunas Terverifikasi, Nomor Resi Pengiriman, Status Trade-In.
- [x] **8.2. Layanan Email SMTP (Laravel Mailables)**
  - [x] Template email HTML responsif bergaya Apple.
  - [x] Otomatisasi lampiran Nota Faktur PDF pada email konfirmasi pembayaran.
- [x] **8.3. Orkestrator Notifikasi & Logging**
  - [x] `NotificationOrchestrator`: Mengirim notifikasi WhatsApp dan Email secara simultan saat terjadi event status pesanan.
  - [x] Pencatatan log ke tabel `notifications_log`.

---

### Phase 9: Generator Nota & Faktur PDF Resmi
- [x] **9.1. Template Desain Nota PDF (DomPDF)**
  - [x] Format A4 elegan bergaya Apple Authorized Partner.
  - [x] Rincian lengkap produk, varian warna/kapasitas, nomor IMEI unit, masa garansi, dan harga.
  - [x] QR Code keaslian nota dan syarat ketentuan klaim garansi.
- [x] **9.2. Fitur Download & Print Faktur**
  - [x] Tombol unduh invoice pada dashboard pesanan customer.
  - [x] Tombol cetak massal invoice dan label pengiriman pada panel admin.

---

### Phase 10: Panel Operasional Admin
- [x] **10.1. Manajemen Inventaris Produk**
  - [x] CRUD Produk & Varian (Warna, Kapasitas, Stok, HPP, Harga Jual, IMEI).
  - [x] Manajemen foto produk multi-angle.
- [x] **10.2. Manajemen Pesanan & Logistik**
  - [x] Filter pesanan berdasarkan status (`pending`, `paid`, `processing`, `shipped`, `completed`).
  - [x] Input nomor resi pengiriman kurir (JNE / SiCepat / J&T) yang otomatis memicu notifikasi WA & Email ke customer.

---

### Phase 11: Dashboard Finansial & Analitik Pemilik Usaha (Owner)
- [x] **11.1. Metrik Keuangan Eksekutif**
  - [x] Total Omzet Penjualan (Harian, Mingguan, Bulanan).
  - [x] Kalkulasi Laba Bersih Otomatis (Omzet - HPP Produk Terjual).
  - [x] Ringkasan arus kas Trade-In masuk vs Penjualan keluar.
- [x] **11.2. Grafik Performa Bisnis**
  - [x] Grafik tren transaksi penjualan 30 hari terakhir.
  - [x] Analisis produk terlaris (*Top Selling iPhone models*).
- [x] **11.3. Laporan & Ekspor Data**
  - [x] Ekspor laporan penjualan dan laba rugi ke format Excel/CSV dan PDF.
  - [x] Manajemen akun staf dan admin toko.

---

### Phase 12: Pengujian, Keamanan, & Finalisasi
- [x] **12.1. Pengujian Integrasi End-to-End**
  - [x] Simulasi checkout dengan Sandbox Midtrans (QRIS & Virtual Account).
  - [x] Pengujian penerimaan webhook Midtrans dan perubahan status pesanan otomatis.
  - [x] Pengujian pengiriman pesan WhatsApp via Fonnte & Email SMTP.
  - [x] Pengujian flow tukar tambah dari submit hingga persetujuan admin.
- [x] **12.2. Validasi UI/UX & Responsivitas**
  - [x] Verifikasi tampilan Glassmorphism pada berbagai resolusi layar (Mobile, Tablet, Desktop).
  - [x] Pengecekan kecepatan halaman dan pemuatan gambar WebP.

---

### Phase 13: Redesign UI/UX v2.0 — Mode Amati Tiru Modifikasi (ATM) Digimap (`https://www.digimap.co.id/`)
- [x] **13.1. Perencanaan & Spesifikasi Dokumen Redesign (`projekiphone/`)**
  - [x] Penyusunan dokumen `projekiphone/redesign.md` dengan analisis ATM (Amati, Tiru, Modifikasi) dari situs resmi Digimap Indonesia (`digimap.co.id`).
  - [x] Penyelarasan spesifikasi bisnis dan fitur baru pada `projekiphone/prd.md`.
  - [x] Pembaruan arsitektur frontend dan sistem desain pada `projekiphone/blueprint.md`.
  - [x] Pembaruan roadmap kerja pada `projekiphone/task.md`.
- [x] **13.2. Transformasi Core CSS ke Digimap Daylight & Frosted White Theme**
  - [x] Pembaruan token warna di `resources/css/glassmorphism.css` & `resources/css/app.css` dari Dark Canvas (`#0B0C10`) ke Digimap Clean White & Warm Gray (`#FFFFFF`, `#F5F5F7`, `#1D1D1F`, `#0066CC`).
  - [x] Penyesuaian class utilitas `.glass-card`, `.glass-navbar`, `.glass-button`, `.glass-input`, dan `.glass-modal` ke gaya *Daylight Frosted Glass* (`rgba(255,255,255,0.84)` + `blur(20px)` + border `#E5E5EA`).
- [x] **13.3. Redesign Master Layout & Navigasi Pelanggan (`layouts/customer.blade.php`)**
  - [x] Implementasi **Top Promo Announcement Bar** (info cicilan, gratis ongkir berasuransi, promo trade-in).
  - [x] Implementasi **Sticky Daylight Header** bersih dengan wordmark tegas, menu seri/kondisi, *Search Pill* (`⌘K`), indikator lokasi gerai (*Click & Collect*), dan badge keranjang.
  - [x] Perombakan Footer bergaya Apple Authorized Partner / Digimap.
- [x] **13.4. Redesign Beranda & Katalog Produk (`home.blade.php`, `catalog.blade.php`, `product-card.blade.php`)**
  - [x] Implementasi **Visual Series Icon Ribbon** untuk navigasi cepat antar seri iPhone.
  - [x] Pembuatan **Hero Promo Banner** bersih bergaya kampanye peluncuran Digimap & **Bento Service Grid**.
  - [x] Pembaruan komponen `<x-product-card>` dengan permukaan `#F5F5F7`, badge kondisi (`Baru • Resmi` vs `Second • Grade A+`), *color swatches*, *storage pills*, harga utama, serta teks simulasi cicilan/tukar tambah.
- [x] **13.5. Redesign Halaman Detail Produk (`pdp.blade.php`) & Alur Transaksi**
  - [x] Layout 2 kolom dengan **Sticky Studio Gallery** (`#F5F5F7`) di kiri dan **Step-by-Step Configurator** di kanan.
  - [x] Integrasi **Inline Trade-In Price Slasher** (kalkulator potongan tukar tambah langsung di PDP) dan modal **Simulasi Cicilan**.
  - [x] Penambahan indikator stok **Click & Collect (Ambil di Toko)** & kurir ekspedisi, serta **Sticky Bottom Purchase Bar**.
  - [x] Penyelarasan visual halaman Keranjang (`cart.blade.php`), Checkout Midtrans (`checkout.blade.php`), Wizard Tukar Tambah (`trade-in.blade.php`), Cek IMEI/Garansi (`utilities.blade.php`), dan Autentikasi (`login`/`register`).
- [x] **13.6. Penyelarasan Panel Admin & Owner ke Digimap Enterprise Light UI**
  - [x] Pembaruan `layouts/admin.blade.php` serta halaman Dashboard Admin & Owner ke tema putih terang bersih (`#FFFFFF` & `#F5F5F7`) untuk kenyamanan operasional harian.

---

### Phase 14: Jalur Login Admin Sendiri (Dedicated Admin Operations Login Portal)
- [x] **14.1. Rute & Controller Autentikasi Khusus Admin (`AdminAuthController`)**
  - [x] Pembuatan rute khusus Admin pada `routes/web.php`: `GET /admin/login` (`admin.login`), `POST /admin/login` (`admin.login.submit`), dan `POST /admin/logout` (`admin.logout`).
  - [x] Pembuatan `App\Http\Controllers\Auth\AdminAuthController` dengan proteksi *brute-force rate limiting* terpisah (`admin-login|{email}|{ip}`).
  - [x] Implementasi *Strict Admin Role Guard*: Memvalidasi bahwa kredensial yang masuk wajib memiliki `role === 'admin'`. Jika akun ber-role `customer` atau `owner` mencoba masuk melalui `/admin/login`, autentikasi ditolak dengan pesan otorisasi spesifik.
- [x] **14.2. Antarmuka Halaman Login Admin Mandiri (`resources/views/auth/admin-login.blade.php`)**
  - [x] Desain halaman login khusus **Rap-iPhone Store Operations Portal** dengan estetika *Digimap Enterprise Light UI* (`#FFFFFF` & `#F5F5F7`).
  - [x] Isolasi penuh dari elemen pelanggan: Tanpa tombol Google OAuth publik dan tanpa tautan pendaftaran akun pelanggan (*register*).
  - [x] Penyediaan *Quick Demo Account Chip* khusus Admin Operasional (`admin@rap-iphone.com`) untuk percepatan pengujian internal.
- [x] **14.3. Pengalihan Otomatis (Redirect) & Proteksi Middleware Admin**
  - [x] Pengalihan pasca-login sukses langsung menuju `route('admin.dashboard')` (`/admin/dashboard`).
  - [x] Pembaruan `RoleMiddleware` agar pengguna *guest* (belum terautentikasi) yang mengakses prefix `/admin/*` otomatis diarahkan ke `route('admin.login')` (`/admin/login`).
  - [x] Pengalihan pasca-logout dari panel operasional admin kembali menuju halaman `/admin/login`.

---

### Phase 15: Jalur Login Owner Sendiri (Dedicated Executive Owner Login Portal)
- [x] **15.1. Rute & Controller Autentikasi Eksklusif Owner (`OwnerAuthController`)**
  - [x] Pembuatan rute khusus Pemilik Usaha pada `routes/web.php`: `GET /owner/login` (`owner.login`), `POST /owner/login` (`owner.login.submit`), dan `POST /owner/logout` (`owner.logout`).
  - [x] Pembuatan `App\Http\Controllers\Auth\OwnerAuthController` dengan proteksi *rate limiting* khusus eksekutif (`owner-login|{email}|{ip}`).
  - [x] Implementasi *Strict Owner Role Guard*: Hanya mengizinkan akun dengan `role === 'owner'` untuk masuk melalui `/owner/login`; menolak tegas percobaan login dari akun `admin` maupun `customer`.
- [x] **15.2. Antarmuka Halaman Login Eksekutif Owner (`resources/views/auth/owner-login.blade.php`)**
  - [x] Desain halaman login eksklusif **Rap-iPhone Executive & Financial Portal** bergaya *Apple Executive Daylight & Titanium Accent*.
  - [x] Antarmuka eksekutif bebas distraksi tanpa opsi registrasi publik dan tanpa tombol Google OAuth pelanggan.
  - [x] Penyediaan *Quick Demo Account Chip* khusus Pemilik Usaha (`owner@rap-iphone.com`) untuk kemudahan pengujian.
- [x] **15.3. Pengalihan Otomatis (Redirect) & Proteksi Middleware Owner**
  - [x] Pengalihan pasca-login sukses langsung menuju `route('owner.dashboard')` (`/owner/dashboard`).
  - [x] Pembaruan `RoleMiddleware` agar pengguna *guest* yang mencoba mengakses prefix `/owner/*` otomatis diarahkan ke `route('owner.login')` (`/owner/login`).
  - [x] Pengalihan pasca-logout dari dashboard finansial owner kembali menuju halaman `/owner/login`.

---

### Phase 16: Jalur Login dan Register Customer Sendiri (Dedicated Customer Auth & Registration Portal)
- [x] **16.1. Pemurnian Rute & Controller Autentikasi Pelanggan (`AuthController` / `CustomerAuthController` & `GoogleAuthController`)**
  - [x] Penataan jalur autentikasi mandiri khusus pelanggan pada `GET /login` (`login`), `POST /login`, `GET /register` (`register`), `POST /register`, serta alias `customer.login` dan `customer.register`.
  - [x] Implementasi *Strict Customer Role Guard* pada halaman `/login` pelanggan: Hanya mengizinkan akun dengan `role === 'customer'`. Apabila akun internal (`admin` atau `owner`) mencoba masuk di halaman login pelanggan, autentikasi ditolak dan diarahkan ke portal internal masing-masing (`/admin/login` atau `/owner/login`).
  - [x] Pengamanan `GoogleAuthController` (`/auth/google` & `/auth/google/callback`): Memastikan autentikasi & pembuatan akun 1-klik Google OAuth dikhususkan murni bagi akun ber-role `customer` serta menolak email yang terdaftar sebagai `admin` atau `owner`.
- [x] **16.2. Pemurnian Antarmuka Login & Registrasi Mandiri Pelanggan (`resources/views/auth/login.blade.php` & `register.blade.php`)**
  - [x] Pembersihan halaman `resources/views/auth/login.blade.php` dari *Quick Demo Chips* milik Admin dan Owner, sehingga hanya menampilkan *Quick Demo Chip* khusus Pelanggan (`customer@rap-iphone.com`) dan fitur relevan belanja B2C.
  - [x] Penyempurnaan halaman registrasi mandiri pelanggan (`resources/views/auth/register.blade.php`) dengan validasi Nama Lengkap, Email, Nomor WhatsApp aktif (untuk notifikasi Fonnte), Kata Sandi (Bcrypt), persetujuan Syarat & Ketentuan, serta opsi *Daftar 1-Klik dengan Google*.
  - [x] Penambahan informasi dan tautan bantuan pelanggan yang jelas antara halaman `/login` dan `/register`.
- [x] **16.3. Sinkronisasi Navigasi Layout & Pengujian Isolasi Lintas-Jalur (End-to-End Multi-Portal Auth)**
  - [x] Penyelarasan tombol Masuk, Daftar, dan Logout pada `layouts/customer.blade.php` dan `layouts/admin.blade.php` agar sesuai dengan jalur autentikasi masing-masing role (`customer`, `admin`, `owner`).
  - [x] Pengujian menyeluruh skenario positif dan negatif (*cross-role login rejection* & *contextual guest redirect*) pada ketiga jalur autentikasi mandiri (Fase 14, Fase 15, dan Fase 16).


---

### Phase 17: Fondasi Admin Design System, Perapian Layout & Dashboard Admin (`http://127.0.0.1:8000/admin/dashboard`)
> **Konteks Fase 17–24:** Perbaikan UI/UX panel Admin & Owner yang masih berantakan. Akar masalah: view admin/owner masih memakai kelas tema gelap (`text-white`, `bg-[#161B22]`, `text-[#8E8E93]`, `border-white/10`) di atas layout terang, tanpa pola halaman yang baku. Perubahan terbatas pada lapisan presentasi (Blade, CSS, JS ringan).
- [x] **17.1. Admin Design System (CSS & Komponen Blade)**
  - [x] Pembuatan `resources/css/admin.css` (di-`@import` dari `app.css`) berisi kelas `adm-page`, `adm-page-header`, `adm-card`, `adm-stat`, `adm-toolbar`, `adm-tabs`, `adm-table`, `adm-field`, `adm-input`, `adm-badge`, `adm-empty`, `adm-num`.
  - [x] Pembuatan komponen Blade `<x-admin.page-header>`, `<x-admin.stat-card>`, `<x-admin.card>`, `<x-admin.empty-state>`, `<x-admin.status-badge>` pada `resources/views/components/admin/`.
- [x] **17.2. Perapian Layout `layouts/admin.blade.php`**
  - [x] Perbaikan konflik latar sidebar & z-index topbar, state aktif menu *Konfigurasi & API*, breadcrumb sadar-role (Admin / Owner), footer disederhanakan, sidebar mobile menutup otomatis, atribut aksesibilitas, dan Lucide dipin ke versi tetap.
  - [x] Badge sidebar (pesanan perlu diproses, trade-in menunggu inspeksi) dan chip status Midtrans/Fonnte memakai data nyata via `View::composer`; lonceng palsu dihapus/diganti tautan antrean.
- [x] **17.3. Redesign `/admin/dashboard`**
  - [x] `page-header` + empat `stat-card` (Pesanan Perlu Diproses, Trade-In Menunggu Inspeksi, Stok Menipis, Total Produk) + panel **Perlu Tindakan** (pesanan terbaru & trade-in `submitted`) + panel *Sesi Anda*.

---

### Phase 18: Perapian Manajemen Produk & Inventaris (`http://127.0.0.1:8000/admin/products`)
- [x] **18.1. Daftar Produk (`admin/products/index.blade.php`)**
  - [x] Migrasi seluruh kelas gelap ke `adm-*`; gabungkan tab kondisi & filter dalam satu toolbar; tabel berkolom tetap dengan thumbnail, indikator stok menipis, aksi ikon, dan state kosong.
- [x] **18.2. Form Tambah Produk (`create.blade.php`)**
  - [x] Form dibagi menjadi kartu bernomor (Info Produk, Garansi & Kondisi, Deskripsi) dengan label di atas input dan error per field.
- [x] **18.3. Form Edit Produk, Varian & Galeri (`edit.blade.php`)**
  - [x] Tiga bagian jelas: Info Produk, Tabel Varian + form tambah varian, Galeri Foto (grid thumbnail, badge "Utama"). Nama field & action form tetap.

---

### Phase 19: Perapian Manajemen Pesanan & Logistik (`http://127.0.0.1:8000/admin/orders`)
- [x] **19.1. Daftar Pesanan (`admin/orders/index.blade.php`)**
  - [x] Kartu ringkasan per status sebagai filter, toolbar pencarian + tab status, tabel (Nota, Pelanggan, Item, Total rata kanan, Status, Kurir/Resi, Aksi), bar aksi massal cetak nota/label, pagination bergaya `adm`.
- [x] **19.2. Detail Pesanan (`admin/orders/show.blade.php`)**
  - [x] Layout 2 kolom: item & timeline status di kiri; pelanggan, pembayaran, form resi, aksi status di kanan; stepper sesuai state machine PRD.

---

### Phase 20: Perapian Antrean Verifikasi Tukar Tambah (`http://127.0.0.1:8000/admin/trade-ins`)
- [x] **20.1. Daftar Antrean Trade-In (`admin/trade-ins/index.blade.php`)**
  - [x] Tab status dengan hitungan, tabel (Kode, Pelanggan, Model/Kapasitas, BH%, Estimasi, Harga Deal, Status).
- [x] **20.2. Detail & Inspeksi Trade-In (`admin/trade-ins/show.blade.php`)**
  - [x] Spesifikasi unit & rekening di kiri; form inspeksi, harga final, tombol Setujui/Tolak dengan konfirmasi di kanan; estimasi vs harga deal bersisian.

---

### Phase 21: Perapian Dashboard Eksekutif Owner (`http://127.0.0.1:8000/owner/dashboard`)
- [x] **21.1. Pemecahan & Perapian Struktur Halaman**
  - [x] Perbaikan karakter rusak pada judul halaman; pemecahan file besar menjadi partial (`owner/partials/_kpi-cards`, `_sales-trend`, `_top-products`, `_cashflow`).
- [x] **21.2. KPI & Grafik**
  - [x] Satu baris 4 KPI (Omzet, Laba Bersih, Pesanan, Konversi) dengan segmented control Harian/Mingguan/Bulanan; grafik SVG berformat sumbu ringkas dan legenda jelas; Top 5 produk & arus kas dalam grid 2 kolom.

---

### Phase 22: Perapian Laporan Keuangan & Ekspor (`http://127.0.0.1:8000/owner/financial-report`)
- [x] **22.1. Toolbar Periode & Ekspor**
  - [x] Filter preset + tanggal kustom dirapikan dalam satu toolbar; tombol Ekspor CSV/PDF berdekatan di header.
- [x] **22.2. Ringkasan P&L & Tabel Rincian**
  - [x] Deret `stat-card` Omzet → HPP → Laba Kotor → Laba Bersih; tabel rincian dengan angka rata kanan, baris total tebal, header sticky. Template PDF tidak diubah.

---

### Phase 23: Perapian Manajemen Staf & Admin Toko (`http://127.0.0.1:8000/owner/staff`)
- [x] **23.1. Tabel Staf**
  - [x] Avatar inisial, nama/email, role (`status-badge`), tanggal terdaftar, aksi.
- [x] **23.2. Modal Tambah/Edit & Konfirmasi Hapus**
  - [x] Form tambah dan edit responsif, dialog konfirmasi hapus; route & nama field tetap.

---

### Phase 24: Perapian Konfigurasi & API, Pembersihan CSS, dan QA Akhir (`http://127.0.0.1:8000/admin/settings`)
- [x] **24.1. Halaman Konfigurasi & Status Integrasi (`admin/settings.blade.php`)**
  - [x] Kartu integrasi seragam (ikon, badge status, endpoint dengan tombol salin), log notifikasi sebagai `adm-table`. Tetap read-only.
- [x] **24.2. Pembersihan Tambalan CSS**
  - [x] Penghapusan blok `.daylight-enterprise …` (dengan `!important`) dari `glassmorphism.css` setelah semua view dimigrasi; pemindaian nol sisa kelas gelap di `resources/views/admin` & `resources/views/owner`.
- [x] **24.3. QA Lintas Halaman**
  - [x] `php artisan test` hijau, `npm run build` sukses, verifikasi responsif (390/768/1440 px), kontras, fokus keyboard, dan state kosong.
