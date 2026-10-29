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

### Phase 17: Hallmark Luxury Editorial Foundation, Audit Awal & Redesign Dashboard Admin (`/admin/dashboard`)
> **Konteks Fase 17–24:** Transformasi panel Admin & Owner menggunakan kerangka kerja **Hallmark Redesign** dengan estetika **Luxury / Editorial** (genre: `editorial`). Menggantikan pola dasbor generik dengan tata letak buku besar mewah (*master trade ledger*), kartu *cotton stock*, tipografi display serif anggun utama (*Sentient* 300/400 via Fontshare), font grotesque presisi (*Hanken Grotesk* 400/500 via Google Fonts), palet limewash hangat, aksen titanium slate, serta penegakan ketat aturan *The Floor* dari Hallmark. Logika backend (routes, controllers, validasi) tetap utuh 100%.
- [x] **17.1. Hallmark Scaffold, Token CSS & Komponen Blade Editorial**
  - [x] Audit awal halaman dengan sloplint: `node .agent/skills/hallmark/scripts/sloplint.mjs resources/views/admin/dashboard.blade.php --genre editorial --render --before`.
  - [x] Pembuatan token sistem Luxury/Editorial pada `resources/css/admin.css` (kanvas limewash, tinta obsidian, hairline rules, font Sentient & Hanken Grotesk).
  - [x] Pembuatan komponen Blade editorial: `<x-admin.page-header>`, `<x-admin.stat-card>` (Statement size), `<x-admin.card>` (Cotton stock), `<x-admin.table>` (Trade ledger), `<x-admin.status-badge>`.
- [x] **17.2. Redesign Master Layout Admin (`layouts/admin.blade.php`)**
  - [x] Transformasi sidebar menjadi editorial masthead dengan hairline divider dan pencilled counters.
  - [x] Penyelarasan topbar, breadcrumb, dan status koneksi API (Midtrans & Fonnte) dengan indikator terukur monokromatik.
- [x] **17.3. Redesign Dashboard Admin Operasional (`admin/dashboard.blade.php`)**
  - [x] Headline editorial, deret 4 kartu KPI bertipe *Statement Numbers*, dan panel antrean *Action Required* bergaya buku catatan kurasi.

---

### Phase 18: Hallmark Redesign Manajemen Produk & Inventaris (`/admin/products`)
- [x] **18.1. Katalog Produk Bergaya Koleksi Maison (`index.blade.php`)**
  - [x] Format Trade Ledger dengan thumbnail berbingkai mat, indikator stok tabular, filter seri minimalis.
- [x] **18.2. Formulir Kurasi & Tambah Produk (`create.blade.php`)**
  - [x] Form berpartisi kartu cotton stock dengan input bergaris bawah (*ruled lines*) dan label wall-text terstruktur.
- [x] **18.3. Galeri & Manajemen Varian (`edit.blade.php`)**
  - [x] Grid galeri berlabel studio, tabel varian dengan snapshot HPP/harga jual tabular, dan form varian presisi.

---

### Phase 19: Hallmark Redesign Manajemen Pesanan & Logistik (`/admin/orders`)
- [x] **19.1. Daftar Transaksi Buku Besar / Master Ledger (`index.blade.php`)**
  - [x] Tabel transaksi ber-double rule, angka moneter rata kanan tabular, badge status stempel (*Settled, Dispatched, Cancelled*).
- [x] **19.2. Lembar Faktur & Rincian Pesanan (`show.blade.php`)**
  - [x] Layout 2 kolom editorial: timeline pengiriman dan rincian unit IMEI di kiri; ringkasan pelanggan, resi kurir, dan aksi status di kanan.

---

### Phase 20: Hallmark Redesign Antrean & Verifikasi Tukar Tambah (`/admin/trade-ins`)
- [x] **20.1. Antrean Inspeksi Unit Trade-In (`index.blade.php`)**
  - [x] Tab status berhitung pencilled, tabel daftar pengajuan dengan parameter Battery Health dan grade fisik.
- [x] **20.2. Lembar Kerja Penilaian & Inspeksi Teknisi (`show.blade.php`)**
  - [x] Tampilan komparasi berdampingan antara kondisi deklarasi pelanggan vs hasil verifikasi teknisi toko, komparasi harga estimasi vs harga final.

---

### Phase 21: Hallmark Redesign Dashboard Eksekutif Owner (`/owner/dashboard`)
- [x] **21.1. Restrukturisasi Partial Blade Eksekutif**
  - [x] Pemecahan modular: `_kpi-cards.blade.php`, `_sales-trend.blade.php`, `_top-products.blade.php`, `_cashflow.blade.php`.
- [x] **21.2. Metrik Finansial & Visualisasi Data Editorial**
  - [x] 4 Statement Figures (Omzet, Laba Bersih, Transaksi Selesai, Konversi) berbobot tipografi mewah; grafik tren garis halus tanpa ornamen berlebihan; Top 5 iPhone terlaris dalam kartu koleksi.

---

### Phase 22: Hallmark Redesign Laporan Keuangan & Ekspor Eksekutif (`/owner/financial-report`)
- [x] **22.1. Toolbar Periode & Aksi Ekspor**
  - [x] Segmented control waktu dan tombol ekspor CSV/PDF berbingkai hairline tinta.
- [x] **22.2. Neraca Laba Rugi (P&L Ledger)**
  - [x] Rantai metrik Omzet -> HPP -> Laba Kotor -> Laba Bersih; tabel buku besar finansial dengan total bergaris ganda (*double underline*).

---

### Phase 23: Hallmark Redesign Manajemen Staf & Hak Akses Toko (`/owner/staff`)
- [x] **23.1. Direktori Staf Toko (`index.blade.php`)**
  - [x] Tabel direktori staf dengan inisial monogram, badge peran terkurasi, dan status akun.
- [x] **23.2. Modal Form Staf & Dialog Otorisasi**
  - [x] Modal sheet dengan estetika letterpress, validasi field terperinci, dan konfirmasi proteksi aksi.

---

### Phase 24: Hallmark Final Audit, Sloplint Sweep & Quality Assurance (`/admin/settings`)
- [x] **24.1. Halaman Konfigurasi Toko & Status Gateway (`admin/settings.blade.php`)**
  - [x] Panel status integrasi Midtrans, Fonnte, SMTP, dan Google OAuth dengan credential cards dan log aktivitas.
- [x] **24.2. Pembersihan Sisa Utilitas CSS & Penegakan The Floor**
  - [x] Pemindaian nol sisa kelas tema gelap lama, memastikan 100% kepatuhan token `--adm-*` dan The Floor.
- [x] **24.3. Eksekusi Linter Sloplint & QA Komprehensif**
  - [x] Eksekusi `node .agent/skills/hallmark/scripts/sloplint.mjs resources/views/admin resources/views/owner --genre editorial`.
  - [x] Verifikasi uji otomatis (`php artisan test`), kompilasi aset (`npm run build`), dan uji navigasi keyboard di berbagai ukuran layar.

---

### Phase 25: Hallmark Customer Design Foundation, Token CSS & Master Layout (`layouts/customer.blade.php`)
> **Konteks Fase 25–32:** Transformasi antarmuka etalase belanja pelanggan (*Customer Storefront*) menggunakan kerangka kerja **Hallmark Redesign** dengan estetika **Luxury Retail & Studio Precision** (genre: `editorial` / `modern-minimal` hybrid). Mengintegrasikan standar ketat aturan *The Floor* dari Hallmark (bebas warna literal, zero `transition: all`, container terpusat, `overflow-x: clip`, rasio 80 kata per layar, `:focus-visible` instan, data produk dan IMEI riil tanpa testimoni fiktif) berpadu harmonis dengan anatomi pekerjaan pembeli (*job: buy*) serta DNA resmi Apple/Digimap Daylight. Seluruh rute belanja, keranjang, checkout Midtrans Snap, wizard trade-in, dan webhook backend dipertahankan utuh 100%.
- [x] **25.1. Audit Awal Sloplint & Setup Token CSS Hallmark Customer (`resources/css/customer.css`)**
  - [x] Audit awal halaman customer dengan sloplint: `node .agent/skills/hallmark/scripts/sloplint.mjs resources/views/customer --genre editorial --render --before`.
  - [x] Pembuatan token sistem Luxury Retail di `resources/css/customer.css` (kanvas limewash alabaster `--cust-paper: oklch(97.2% 0.006 215)`, kartu cotton stock `--cust-surface: #FFFFFF`, tinta obsidian pekat `--cust-ink: oklch(16% 0.032 250)`, teks sekunder slate `--cust-ink-muted`, aksen cool slate titanium `--cust-accent: oklch(41% 0.075 248)` & retail blue `--cust-signal: #0071E3`, font Sentient serif + Hanken Grotesk / Geist dengan `tabular-nums`).
  - [x] Penegakan ketat 16 aturan *The Floor* dari Hallmark (bebas warna literal, zero `transition: all`, no card-in-card, no tracked capitals, no gradient ungu-ke-biru, `overflow-x: clip`, `:focus-visible` seketika).
  - [x] Pendaftaran `@import './customer.css';` di `resources/css/app.css`.
- [x] **25.2. Redesign Master Layout Pelanggan (`layouts/customer.blade.php`)**
  - [x] Top Promo Announcement Bar minimalis satu kalimat terukur (cicilan, ongkir berasuransi, trade-in) tanpa emotikon norak.
  - [x] Sticky Header Daylight Frosted (`--cust-glass`) dengan wordmark presisi, navigasi seri horizontal, search pill (`⌘K`), badge keranjang tabular, dan dropdown profil pelanggan mandiri.
  - [x] Footer editorial kurasi: informasi gerai fisik Click & Collect, izin usaha resmi, jam operasional, link navigasi bersih, dan kredensial partner tanpa logo wall fiktif.
- [x] **25.3. Komponen Blade Customer Reusable**
  - [x] Pembuatan komponen Blade kurasi: `<x-customer.product-card>`, `<x-customer.spec-badge>`, `<x-customer.button>`, `<x-customer.color-swatch>`, `<x-customer.battery-health-meter>`, `<x-customer.section-header>`.

---

### Phase 26: Hallmark Redesign Beranda & Showcase Utama Customer (`customer/home.blade.php`)
- [x] **26.1. Hero Flagship Showcase (Job: buy — Offer as Hero)**
  - [x] Tampilan hero Apple flagship: headline maksimal 7 kata, 1 kalimat penjelas (maksimal 12 kata), dan tombol aksi ganda (*Beli Sekarang* & *Simulasi Trade-In*), tampak utuh pada resolusi 1280x800 tanpa scroll (F12).
  - [x] Visual unit iPhone resolusi tinggi dalam pencahayaan studio alami (*true color photography*), tanpa latar gradien AI slop.
- [x] **26.2. Visual Series Icon Ribbon & Curated Bento Grid**
  - [x] Ribbon navigasi seri horizontal (*iPhone 16 Pro, 16, 15 Pro, 15, 14, 13, Second Like New, Trade-In*) dengan indikator aktif minimalis.
  - [x] Bento Grid layanan toko (Tukar Tambah 15 Menit, Garansi Resmi TAM/iBox vs Second QC 32-Titik, Click & Collect Gerai Store).
- [x] **26.3. Katalog Unggulan Beranda (Featured Product Rail)**
  - [x] Grid koleksi iPhone unggulan dengan switcher kondisi instan, titik warna bodi aktual, dan informasi harga jujur (*honest pricing*).

---

### Phase 27: Hallmark Redesign Katalog & Filter Multi-Kondisi (`customer/catalog.blade.php`)
- [x] **27.1. Macrostructure Catalogue (11) — Visual Index of Inventory**
  - [x] Struktur indeks inventaris jujur bergrid rapi tanpa kartu bersarang (*card-in-card*).
- [x] **27.2. Dual-Condition Segmented Control & Filter Toolbar**
  - [x] Segmented control minimalis: **Semua**, **Unit Baru Garansi Resmi**, dan **Second Like New Teruji**.
  - [x] Toolbar filter bergaris hairline: seri iPhone, kapasitas penyimpanan (128GB–1TB), rentang harga, dan status ketersediaan.
- [x] **27.3. Grid Kartu Produk Interaktif**
  - [x] Kartu produk berkanvas cotton stock dengan *color swatch dots* (Natural Titanium, Desert Titanium, Black, White), badge kondisi stempel kurasi, indikator *Battery Health* (untuk unit second), dan harga moneter tabular.

---

### Phase 28: Hallmark Redesign Halaman Detail Produk / PDP (`customer/pdp.blade.php`)
- [x] **28.1. Macrostructure Split Studio (15) — Sticky Studio Gallery**
  - [x] Kolom kiri: Galeri studio bertingkat yang menempel (*sticky*) saat scroll, rasio aspek presisi, thumbnail multi-angle, tanpa mockup fiktif.
- [x] **28.2. Step-by-Step Configurator & Live Dynamic Pricing**
  - [x] Kolom kanan: Pemilih kondisi unit (Baru vs Second Grade A+), pemilih varian warna Apple resmi dengan feedback nama warna, dan tombol pill kapasitas (128GB–1TB) dengan update harga seketika.
- [x] **28.3. Transparency Trust Panel (Khusus Unit Second)**
  - [x] Panel transparansi data riil: persentase *Battery Health* aktual, nomor IMEI fisik terverifikasi Kemenperin, dan ceklis kelulusan QC 32 titik.
- [x] **28.4. Dual Price Estimator & Sticky Purchase Bar**
  - [x] Kalkulator simulasi cicilan per bulan dan kalkulator diskon tukar tambah (*inline trade-in estimator*).
  - [x] Sticky Bottom Purchase Bar responsif dengan ringkasan varian terpilih dan CTA pembelian instan.

---

### Phase 29: Hallmark Redesign Keranjang Belanja & Alur Checkout Midtrans (`customer/cart.blade.php` & `customer/checkout.blade.php`)
- [x] **29.1. Lembar Keranjang Belanja Bergaya Spec Sheet (`cart.blade.php`)**
  - [x] Rincian item belanja bergaris hairline, thumbnail bertanda kurasi, kontrol kuantitas presisi, dan ringkasan subtotal tabular.
- [x] **29.2. Halaman Checkout Terpadu Bebas Distraksi (`checkout.blade.php`)**
  - [x] Form data penerima dengan pembagian jelas: Pengiriman Ekspedisi Berasuransi (JNE/SiCepat/J&T) atau *Click & Collect* (Ambil di Toko).
  - [x] Ringkasan pesanan bertipe buku kas master ledger dengan rincian ongkir, diskon trade-in, dan total tagihan.
  - [x] Integrasi modal Midtrans Snap ber-focus ring instan dan proteksi submit ganda.

---

### Phase 30: Hallmark Redesign Wizard Jual & Tukar Tambah iPhone (`customer/trade-in.blade.php` & `trade-in-detail.blade.php`)
- [x] **30.1. 4-Step Interactive Trade-In Wizard (Macrostructure: Narrative Workflow)**
  - [x] Tahap 1: Pilih Seri & Model iPhone lama.
  - [x] Tahap 2: Pilih Kapasitas & Indikasi Battery Health.
  - [x] Tahap 3: Ceklis Kondisi Fisik, Layar, Kelengkapan Unit.
  - [x] Tahap 4: Lembar Estimasi Nilai Tukar Tambah Instan (*Instant Appraisal Quote*).
- [x] **30.2. Pilihan Pencairan Dana & Integrasi Keranjang**
  - [x] Pilihan langsung: Klaim sebagai voucher potongan belanja di keranjang atau transfer rekening bank customer.
  - [x] Halaman rincian pengajuan trade-in pelanggan (`trade-in-detail.blade.php`) dengan timeline status verifikasi toko.

---

### Phase 31: Hallmark Redesign Riwayat Pesanan, Pelacakan Resi & Nota PDF (`customer/orders.blade.php` & `checkout-success.blade.php`)
- [x] **31.1. Layar Konfirmasi Pembayaran Sukses (`checkout-success.blade.php`)**
  - [x] Tampilan tanda terima resmi lunas seketika pasca-transaksi Midtrans, info estimasi pengiriman, dan tombol unduh nota.
- [x] **31.2. Dashboard Riwayat Pesanan Pelanggan (`customer/orders.blade.php`)**
  - [x] Buku besar transaksi pelanggan: daftar pesanan dengan badge stempel status (*Paid, Shipped, Completed*), tracking nomor resi ekspedisi, dan tombol unduh faktur PDF DomPDF resmi.

---

### Phase 32: Hallmark Customer Final Sweep, Sloplint Audit, The Floor Compliance & QA
- [x] **32.1. Audit Sloplint Seluruh Halaman Customer**
  - [x] Eksekusi linter Hallmark: `node .agent/skills/hallmark/scripts/sloplint.mjs resources/views/customer resources/views/layouts/customer.blade.php --genre editorial`.
  - [x] Perbaikan 100% temuan FAIL dan verifikasi kepatuhan The Floor (F1–F16).
- [x] **32.2. Validasi Kepatuhan Aksesibilitas & Responsivitas**
  - [x] Uji navigasi keyboard `:focus-visible` di seluruh kontrol formulir dan tombol aksi.
  - [x] Uji tampilan responsif di mobile (375px), tablet (768px), laptop (1280px), dan desktop (1440px / 1920px terpusat).
- [x] **32.3. Pengujian Fungsional End-to-End (E2E)**
  - [x] Verifikasi alur belanja penuh: Katalog -> PDP (Pilih Varian) -> Tambah Keranjang -> Checkout Midtrans -> Webhook Callback -> Notifikasi WA Fonnte & Email SMTP -> Faktur PDF.
  - [x] Eksekusi suite pengujian Laravel: `php artisan test` dan kompilasi aset `npm run build`.

---

### Phase 33: Audit, Review, & Perbaikan Logika Bisnis Alur Kerja Terpadu Customer – Admin – Owner (End-to-End Workflow Verification & Business Logic Rectification)
- [x] **33.1. Audit & Validasi Alur Transaksi Customer $\rightarrow$ Midtrans $\rightarrow$ Admin Fulfillment**
  - [x] Audit validasi ketersediaan stok & row-level locking pada saat checkout (`CheckoutController` & `MidtransCallbackController`).
  - [x] Audit alur nomor resi kurir, alokasi nomor IMEI fisik, dan trigger otomatis notifikasi WhatsApp (Fonnte) & Email (SMTP).
  - [x] Audit sinkronisasi status pesanan (`pending` -> `paid` -> `processing` -> `shipped` -> `completed` / `cancelled`).
- [x] **33.2. Audit & Validasi Alur Program Tukar Tambah (Trade-In) Customer $\rightarrow$ Teknisi Admin**
  - [x] Audit kalkulasi estimasi nilai unit lama pada `TradeInCalculatorService` & `TradeInCustomerController`.
  - [x] Audit form inspeksi fisik teknisi toko, validasi penawaran harga final, dan keputusan (`approved`, `rejected`, `paid_out`).
  - [x] Audit metode pencairan dana: Transfer Rekening vs Kupon Voucher Belanja Toko terintegrasi di checkout.
- [x] **33.3. Audit & Validasi Akurasi Finansial Eksekutif Owner (HPP, Laba Bersih, Arus Kas)**
  - [x] Audit integritas snapshot `cost_price` (HPP) historis pada `order_items`.
  - [x] Audit formula Laba Kotor, Diskon, Laba Bersih, dan Arus Kas Bersih pada `FinancialAnalyticsService`.
  - [x] Audit integritas ekspor CSV (UTF-8 BOM untuk Microsoft Excel) dan PDF Laba Rugi Eksekutif.
- [x] **33.4. Audit Batasan Hak Akses & Isolasi Autentikasi Multi-Portal (Role Boundaries & Staff Management)**
  - [x] Audit proteksi penolakan login silang (*cross-portal rejection*) antar `/login`, `/admin/login`, dan `/owner/login`.
  - [x] Audit isolasi akses data pesanan dan faktur (Customer hanya akses pesanan sendiri, Admin & Owner akses penuh).
  - [x] Audit proteksi akun pada modul manajemen staf (pencegahan Owner menghapus akun dirinya sendiri).
- [x] **33.5. Penulisan Automated End-to-End Feature Test & Eksekusi QA Komprehensif**
  - [x] Pembuatan test suite terpadu `tests/Feature/EndToEndWorkflowTest.php` yang menguji siklus lengkap Customer -> Midtrans -> Admin -> Owner.
  - [x] Verifikasi seluruh test suite lulus 100% (`php artisan test` - 152 passed, 1081 assertions).

---

### Phase 34: System Security Hardening, IDOR Mitigation, ACID Compliance, Race Condition Prevention & Defense-in-Depth Verification
> **Konteks Fase 34:** Penguatan ketahanan sistem platform Rap-iPhone terhadap ancaman siber enterprise, integritas data finansial, serta proteksi konkurensi tinggi. Menegakkan prinsip IDOR (Insecure Direct Object Reference) melalui Policies & Gates, transaksi database ACID multi-tabel pada MySQL InnoDB, pencegahan Race Condition & overselling stok via *pessimistic row locking* (`lockForUpdate()`) dan *atomic decrement*, proteksi *double-spending* kupon voucher trade-in, isolasi rate limiting anti-brute-force multi-portal, mitigasi session fixation, integritas kriptografis HMAC QR Code invoice, penyamaran privasi pelanggan (PII), serta pengujian otomatis keamanan berlapis.

- [x] **34.1. Audit & Penguatan IDOR (Insecure Direct Object Reference) Protection**
  - [x] Pembuatan `App\Policies\TradeInPolicy` dengan metode `view`, `update`, dan `cancel`, serta pendaftaran pada service provider.
  - [x] Penegakan otorisasi IDOR pada endpoint `GET /checkout/success/{order:order_number}` (`CheckoutController::success`) untuk memastikan hanya pembeli sah yang dapat melihat tanda terima pembayaran.
  - [x] Penegakan otorisasi IDOR pada endpoint invoice `GET /orders/{order:order_number}/invoice` (`InvoiceController`) via `OrderPolicy@downloadInvoice` untuk mencegah pelanggan mengunduh faktur orang lain.
  - [x] Penegakan isolasi data riwayat pesanan `GET /customer/orders` dan profil customer murni berbasis `auth()->id()`.
  - [x] Verifikasi pencegahan eskalasi hak akses horizontal dan vertikal pada seluruh endpoint Admin dan Owner (`RoleMiddleware` & Policy).
- [x] **34.2. Penegakan Kepatuhan Transaksi Database ACID Multi-Tabel**
  - [x] Pembungkusan alur pembuatan pesanan di `CheckoutController::process` dalam `DB::transaction()` terisolasi dengan rollback menyeluruh jika terjadi kegagalan Midtrans atau pembuatan item pesanan.
  - [x] Atomisitas perubahan status pembayaran dan mutasi kuantitas stok pada `MidtransCallbackController::handle` dengan proteksi rollback penuh.
  - [x] Atomisitas persetujuan trade-in, penerbitan kupon voucher diskon, dan pencatatan arus kas pada `TradeInVerificationController`.
  - [x] Penegakan integritas foreign key dan invariant constraint `stock >= 0` di level basis data MySQL InnoDB.
- [x] **34.3. Mitigasi Race Condition & Kontrol Konkurensi Tinggi (Pessimistic Locking)**
  - [x] Implementasi pessimistic row-level locking (`ProductVariant::where('id', ...)->lockForUpdate()->first()`) pada saat verifikasi stok checkout dan pengurangan stok webhook settlement.
  - [x] Penerapan atomic decrement query bertingkat (`where('stock', '>=', $qty)->decrement('stock', $qty)`) untuk mencegah *overselling* saat flash sale atau lonjakan trafik.
  - [x] Penguncian baris voucher tukar tambah (`TradeIn::where('customer_account_number', $voucherCode)->lockForUpdate()->first()`) untuk mencegah *double-spending* kupon voucher di beberapa transaksi paralel.
  - [x] Penegakan idempotensi webhook Midtrans dengan row lock pada pesanan untuk mencegah *double stock deduction* saat menerima webhook berulang secara bersamaan.
- [x] **34.4. Proteksi Autentikasi, Rate Limiting Multi-Portal & Session Hardening**
  - [x] Penerapan rate limiting ketat pada `/admin/login` (`throttle:5,1`) dan `/owner/login` (`throttle:3,1`).
  - [x] Penerapan rate limiting pada registrasi dan login customer `/login` & `/register` (`throttle:10,1`).
  - [x] Penerapan rate limiting pada endpoint publik verifikasi nota `/verifikasi-nota/{orderNumber}/{code}` (`throttle:30,1`).
  - [x] Penegakan session regeneration (`$request->session()->regenerate()`) saat login sukses di ketiga portal untuk mematikan risiko *session fixation*.
  - [x] Penegakan pembersihan sesi total (`session()->invalidate()` & `regenerateToken()`) saat logout.
  - [x] Audit konfigurasi cookie sesi (`http_only = true`, `same_site = lax`, `secure = true`).
- [x] **34.5. Sanitasi Input, Proteksi Web (CSRF, XSS, SQLi) & Hardening Upload Berkas**
  - [x] Penegakan token CSRF pada seluruh form Blade dan endpoint AJAX yang memodifikasi status/data.
  - [x] Sanitasi Blade `{{ }}` dan pembersihan input HTML berbahaya pada `customer_notes` dan deskripsi produk.
  - [x] Audit atribut `$fillable` pada seluruh model Eloquent untuk mencegah eksploitasi *mass-assignment* pada atribut sensitif (`role`, `status`, `cost_price`).
  - [x] Validasi ketat unggahan foto produk dan unit tukar tambah: whitelist MIME (`image/jpeg,image/png,image/webp`), batas ukuran berkas maksimum 3MB, dan penamaan hash acak `Str::random(40)` guna menangkal serangan *Path Traversal* dan *PHP Shell Upload*.
- [x] **34.6. Integritas Kriptografis, Anti-Tampering & Perlindungan Privasi (PII)**
  - [x] Verifikasi keaslian webhook Midtrans menggunakan SHA-512 signature hash matching dengan server key rahasia.
  - [x] Verifikasi keaslian QR Code Invoice menggunakan HMAC-SHA256 berbasis `app.key` untuk mencegah manipulasi isi faktur.
  - [x] Penyamaran data sensitif pemesan (nama dan nomor telepon) pada halaman publik verifikasi nota untuk melindungi privasi pelanggan (PII).
- [x] **34.7. Pembuatan Automated Security Test Suite & Eksekusi QA Menyeluruh**
  - [x] Pembuatan test suite terdedikasi `tests/Feature/SecurityHardeningTest.php` yang menguji skenario:
    - Penolakan IDOR pesanan, faktur, dan tukar tambah antar-pengguna (`403 Forbidden`).
    - Simulasi transaksi konkuren / race condition checkout stok terbatas dengan pessimistic lock.
    - Simulasi pencegahan double-spending voucher tukar tambah paralel.
    - Pengujian idempotensi webhook callback ganda.
    - Pengujian rate limiter anti-brute force multi-portal.
    - Pengujian integritas dan anti-tamper QR Code faktur serta masking PII.
  - [x] Eksekusi seluruh pengujian aplikasi (`php artisan test`) untuk memastikan nol regresi fungsional (162 passed, 1120 assertions).

