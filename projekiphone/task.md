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
- [ ] **5.1. Beranda Interaktif (Home Page)**
  - [ ] Hero showcase Apple flagship dengan efek floating unit dan ambient glow.
  - [ ] Kategori seri iPhone (iPhone 16 Series, iPhone 15 Series, dll).
  - [ ] Bagian Unit Baru Garansi Resmi vs Second Like New Teruji.
- [ ] **5.2. Halaman Rincian Produk (Product Detail Page - PDP)**
  - [ ] Galeri gambar interaktif.
  - [ ] Selector warna dinamis (Color dots Apple) yang mengubah gambar aktif.
  - [ ] Selector kapasitas penyimpanan (128GB, 256GB, 512GB, 1TB) dengan pembaruan harga instan (*live price update*).
  - [ ] Widget informasi kesehatan baterai (*Battery Health*) dan IMEI transparan untuk unit second.
  - [ ] Tombol aksi cepat: *"Beli Sekarang"* & *"Tambah ke Keranjang"*.
- [ ] **5.3. Modul Keranjang Belanja (Cart System)**
  - [ ] Tambah unit ke keranjang dengan varian spesifik.
  - [ ] Update kuantitas dan hapus item dengan feedback visual halus.

---

### Phase 6: Checkout Terpadu & Integrasi Midtrans Snap
- [ ] **6.1. Halaman Checkout Ringkas (Distraction-Free)**
  - [ ] Form alamat penerima dan nomor WhatsApp aktif.
  - [ ] Ringkasan rincian pesanan dan total tagihan.
- [ ] **6.2. Layanan Pembayaran Midtrans (`MidtransService`)**
  - [ ] Konfigurasi SDK Midtrans Snap dengan server key.
  - [ ] Generate Snap Token berdasarkan data order unik.
- [ ] **6.3. Integrasi Frontend Midtrans Snap Popup**
  - [ ] Trigger modal popup pembayaran Midtrans saat checkout disubmit.
  - [ ] Penanganan respon frontend (`onSuccess`, `onPending`, `onError`, `onClose`).
- [ ] **6.4. Midtrans Webhook Callback Handler (`/api/midtrans/callback`)**
  - [ ] Verifikasi keamanan SHA512 signature hash.
  - [ ] Pembaruan status pesanan (`settlement` -> `paid`, `cancel` -> `cancelled`).
  - [ ] Database transaction: Pengurangan kuantitas stok varian saat status `paid`.
  - [ ] Idempotency: Menghindari pemrosesan duplikat untuk webhook yang sama.

---

### Phase 7: Modul Jual & Tukar Tambah iPhone (Trade-In Program)
- [ ] **7.1. Wizard Interaktif Pelanggan (Trade-In Wizard)**
  - [ ] Form 4 langkah: Pilih Model -> Kapasitas -> Kondisi Fisik & Fungsi -> Estimasi Nilai.
  - [ ] Kalkulator penaksir nilai otomatis berbasis parameter kondisi unit.
  - [ ] Pilihan pencairan dana: Transfer bank rekening customer atau kupon potongan belanja.
- [ ] **7.2. Panel Verifikasi & Inspeksi Admin**
  - [ ] Antrean pengajuan trade-in customer.
  - [ ] Form inspeksi teknisi toko saat unit diterima.
  - [ ] Fitur penyesuaian penawaran harga final dan tombol persetujuan (*Approve / Reject*).

---

### Phase 8: Otomasi Notifikasi WhatsApp (Fonnte) & Email (SMTP)
- [ ] **8.1. Layanan Notifikasi WhatsApp (`FonnteService`)**
  - [ ] Klien HTTP untuk pengiriman pesan ke API Fonnte.
  - [ ] Template pesan: Tagihan Baru, Pembayaran Lunas Terverifikasi, Nomor Resi Pengiriman, Status Trade-In.
- [ ] **8.2. Layanan Email SMTP (Laravel Mailables)**
  - [ ] Template email HTML responsif bergaya Apple.
  - [ ] Otomatisasi lampiran Nota Faktur PDF pada email konfirmasi pembayaran.
- [ ] **8.3. Orkestrator Notifikasi & Logging**
  - [ ] `NotificationOrchestrator`: Mengirim notifikasi WhatsApp dan Email secara simultan saat terjadi event status pesanan.
  - [ ] Pencatatan log ke tabel `notifications_log`.

---

### Phase 9: Generator Nota & Faktur PDF Resmi
- [ ] **9.1. Template Desain Nota PDF (DomPDF)**
  - [ ] Format A4 elegan bergaya Apple Authorized Partner.
  - [ ] Rincian lengkap produk, varian warna/kapasitas, nomor IMEI unit, masa garansi, dan harga.
  - [ ] QR Code keaslian nota dan syarat ketentuan klaim garansi.
- [ ] **9.2. Fitur Download & Print Faktur**
  - [ ] Tombol unduh invoice pada dashboard pesanan customer.
  - [ ] Tombol cetak massal invoice dan label pengiriman pada panel admin.

---

### Phase 10: Panel Operasional Admin
- [ ] **10.1. Manajemen Inventaris Produk**
  - [ ] CRUD Produk & Varian (Warna, Kapasitas, Stok, HPP, Harga Jual, IMEI).
  - [ ] Manajemen foto produk multi-angle.
- [ ] **10.2. Manajemen Pesanan & Logistik**
  - [ ] Filter pesanan berdasarkan status (`pending`, `paid`, `processing`, `shipped`, `completed`).
  - [ ] Input nomor resi pengiriman kurir (JNE / SiCepat / J&T) yang otomatis memicu notifikasi WA & Email ke customer.

---

### Phase 11: Dashboard Finansial & Analitik Pemilik Usaha (Owner)
- [ ] **11.1. Metrik Keuangan Eksekutif**
  - [ ] Total Omzet Penjualan (Harian, Mingguan, Bulanan).
  - [ ] Kalkulasi Laba Bersih Otomatis (Omzet - HPP Produk Terjual).
  - [ ] Ringkasan arus kas Trade-In masuk vs Penjualan keluar.
- [ ] **11.2. Grafik Performa Bisnis**
  - [ ] Grafik tren transaksi penjualan 30 hari terakhir.
  - [ ] Analisis produk terlaris (*Top Selling iPhone models*).
- [ ] **11.3. Laporan & Ekspor Data**
  - [ ] Ekspor laporan penjualan dan laba rugi ke format Excel/CSV dan PDF.
  - [ ] Manajemen akun staf dan admin toko.

---

### Phase 12: Pengujian, Keamanan, & Finalisasi
- [ ] **12.1. Pengujian Integrasi End-to-End**
  - [ ] Simulasi checkout dengan Sandbox Midtrans (QRIS & Virtual Account).
  - [ ] Pengujian penerimaan webhook Midtrans dan perubahan status pesanan otomatis.
  - [ ] Pengujian pengiriman pesan WhatsApp via Fonnte & Email SMTP.
  - [ ] Pengujian flow tukar tambah dari submit hingga persetujuan admin.
- [ ] **12.2. Validasi UI/UX & Responsivitas**
  - [ ] Verifikasi tampilan Glassmorphism pada berbagai resolusi layar (Mobile, Tablet, Desktop).
  - [ ] Pengecekan kecepatan halaman dan pemuatan gambar WebP.
