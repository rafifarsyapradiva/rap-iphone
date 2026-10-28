# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Platform E-Commerce Jual Beli & Tukar Tambah iPhone (Rap-iPhone)

---

### 1. Ringkasan Eksekutif & Visi Produk
**Rap-iPhone** adalah platform e-commerce web modern dan premium yang dirancang khusus untuk transaksi jual-beli serta tukar tambah (*trade-in / buyback*) produk iPhone. Mengadopsi standar layanan dan *user experience* dari reseller resmi kenamaan seperti **iBox** dan **Digimap**, platform ini memadukan kemudahan alur belanja tanpa ribet, transparansi kondisi unit & garansi, serta estetika visual **Glassmorphism** berkelas tinggi.

Didukung oleh framework **Laravel 12**, basis data **MySQL**, dan template engine **Laravel Blade**, Rap-iPhone dilengkapi integrasi menyeluruh:
- **Google Console Login (OAuth 2.0)** untuk kemudahan masuk 1-klik bagi pelanggan.
- **Midtrans Payment Gateway (Snap)** untuk transaksi aman melalui QRIS, Virtual Account, E-Wallet, dan Kartu Kredit.
- **Notifikasi Otomatis WhatsApp (Fonnte API)** dan **Email (SMTP)** untuk *real-time updates* status transaksi.
- **Nota / Invoice PDF Otomatis** dengan desain resmi untuk bukti pembelian yang sah.
- **Manajemen 3 Tingkat Akses (Role)**: *Customer*, *Admin Operasional*, dan *Pemilik Usaha (Owner)*.

---

### 2. Model Bisnis & Konsep Usaha
Konsep usaha mengadaptasi ekosistem retail iPhone modern (B2C + Program Tukar Tambah):
1. **Penjualan Unit Baru (*Brand New in Box / BNIB*)**:
   - Garansi resmi Indonesia (TAM / iBox / Digimap / GDN).
   - Varian lengkap (seri, kapasitas penyimpanan, warna resmi).
2. **Penjualan Unit Bekas Berkualitas (*Pre-owned / Second Like New*)**:
   - Klasifikasi Grade transparan (Grade A+, Grade A, Grade B).
   - Informasi *Battery Health* (BH) aktual, status IMEI (Kemenperin/Bea Cukai), dan kelengkapan unit (Fullset OEM/Original/Unit Only).
3. **Program Jual / Tukar Tambah (*Trade-In / Buyback*)**:
   - Pelanggan dapat menjual iPhone lama mereka atau menjadikannya potongan harga (*trade-in*) untuk pembelian unit baru.
   - Dilengkapi kalkulator estimasi harga otomatis berbasis kondisi fisik, fungsi, dan kesehatan baterai.

---

### 3. Pengguna & Matriks Hak Akses (Role-Based Access)

| Fitur / Modul | Customer (Pelanggan) | Admin Operasional | Pemilik Usaha (Owner) |
|---|:---:|:---:|:---:|
| Registrasi & Login (Email & Google OAuth) | Ya | Ya (Login) | Ya (Login) |
| Jelajah Katalog & Filter Produk | Ya | Ya | Ya |
| Keranjang & Checkout Midtrans | Ya | - | - |
| Pengajuan Jual / Tukar Tambah iPhone | Ya | - | - |
| Unduh Nota / Invoice PDF | Ya (Pesanan Milik Sendiri) | Ya (Semua Pesanan) | Ya (Semua Pesanan) |
| Pelacakan Status Pesanan & Riwayat | Ya | Ya | Ya |
| Kelola Produk, Varian, IMEI & Stok | - | Ya (Penuh) | Ya (Penuh / Monitoring) |
| Verifikasi & Update Status Pesanan | - | Ya | Ya |
| Inspeksi & Approval Jual/Tukar Tambah | - | Ya | Ya |
| Input Nomor Resi & Logistik | - | Ya | Ya |
| Dashboard Analitik Omzet & Laba Bersih | - | Terbatas (Operasional) | Ya (Penuh / Finansial) |
| Laporan Penjualan & Ekspor Data | - | Laporan Operasional | Laporan Laba Rugi Penuh |
| Manajemen Akun Karyawan / Admin | - | - | Ya |
| Pengaturan Toko & Konfigurasi API | - | Terbatas | Ya |

---

### 4. Fitur Fungsional Utama

#### 4.1. Modul Pelanggan (Customer Experience)
1. **Autentikasi Cepat**:
   - Login konvensional (Email & Password terenkripsi).
   - Login 1-klik dengan Akun Google via Google Console OAuth.
2. **Katalog & Filter Produk Berstandar iBox**:
   - Filter menurut Seri iPhone (iPhone 16 Pro, 16, 15 Pro, 15, 14, 13, dll).
   - Pilihan Warna Interaktif (Visual Dot Warna Apple: *Natural Titanium, Desert Titanium, Midnight, Starlight*, dll).
   - Pilihan Kapasitas Penyimpanan (128GB, 256GB, 512GB, 1TB) dengan pembaruan harga instan (*dynamic pricing*).
   - Filter Kondisi: Baru (Garansi Resmi) vs Second Teruji (dengan indikator Battery Health).
3. **Alur Checkout Mudah & Bebas Ribet**:
   - Checkout langsung (*Buy Now*) atau melalui keranjang belanja (*Cart*).
   - Input alamat pengiriman ringkas dengan kalkulasi ongkir.
   - Integrasi popup **Midtrans Snap**: Pembayaran tanpa meninggalkan halaman dengan pilihan metode lengkap (QRIS, BCA VA, Mandiri VA, BRI VA, BNI VA, GoPay, ShopeePay, Kartu Kredit).
4. **Modul Jual / Tukar Tambah (*Trade-In Wizard*)**:
   - Form interaktif bertahap (Model iPhone lama -> Kapasitas -> Kondisi Fisik Layar/Body -> Kelengkapan & BH).
   - Kalkulasi estimasi harga instan (*instant price quote*).
   - Pilihan: Terima tunai (transfer bank) atau voucher potongan pembelian iPhone baru.
5. **Invoice & Dokumen Transaksi**:
   - Unduh langsung Nota PDF berdesain elegan.
   - Status garansi dan nomor seri/IMEI tertera pada nota.

#### 4.2. Modul Notifikasi Terintegrasi
1. **WhatsApp Notifier (Fonnte API)**:
   - Notifikasi pesanan dibuat & link pembayaran Midtrans.
   - Notifikasi pembayaran berhasil terverifikasi.
   - Notifikasi resi pengiriman & kurir paket.
   - Notifikasi status persetujuan tukar tambah unit.
2. **Email Notifier (SMTP Laravel Mail)**:
   - Email sambutan pendaftaran.
   - Email rincian pesanan dengan lampiran Nota PDF (*Invoice Attachment*).
   - Email notifikasi perubahan status transaksi.

#### 4.3. Modul Admin Operasional
1. **Manajemen Produk & Varian**:
   - Input produk induk (Seri, Deskripsi, Tipe Garansi).
   - Manajemen varian multi-dimensi (Kombinasi Warna + Kapasitas + Stok + IMEI + Harga + Foto Varian).
2. **Manajemen Transaksi & Pesanan**:
   - Monitoring pesanan masuk secara *real-time*.
   - Status otomatis berubah via Midtrans Webhook Callback (*Settlement, Pending, Expire, Cancel*).
   - Form input nomor resi kurir pengiriman (JNE, SiCepat, J&T).
3. **Manajemen Unit Tukar Tambah / Jual iPhone**:
   - Daftar antrean pengajuan dari customer.
   - Form verifikasi fisik unit saat diterima di toko.
   - Penyesuaian penawaran harga final & tombol persetujuan transfer/tukar.

#### 4.4. Modul Pemilik Usaha (Owner Dashboard)
1. **Executive Financial Summary**:
   - Total Omzet Penjualan (Harian, Mingguan, Bulanan, Tahunan).
   - Perhitungan Estimasi Laba Kotor & Laba Bersih (Harga Jual dikurangi HPP unit).
   - Ringkasan Arus Kas Masuk (Penjualan) vs Arus Kas Keluar (Pencairan Trade-In).
2. **Performa Bisnis & Produk Terlaris**:
   - Grafik interaktif tren penjualan.
   - Daftar *Top 5 Best-Selling iPhones* dan varian terpopuler.
   - Tingkat konversi transaksi (Pesanan Selesai vs Batal).
3. **Laporan & Ekspor**:
   - Ekspor laporan penjualan ke format PDF dan Excel/CSV.
   - Kontrol akun admin dan staf.

---

### 5. Kebutuhan Non-Fungsional (NFR)
1. **Keamanan (Security)**:
   - Sanitasi input dan proteksi terhadap CSRF, XSS, serta SQL Injection (Eloquent ORM).
   - Autentikasi aman password hashing via Bcrypt/Argon2.
   - Verifikasi SHA512 Signature Hash pada Webhook Midtrans untuk mencegah manipulasi status transaksi.
2. **Performa & Responsivitas**:
   - Waktu muat halaman pertama di bawah 2 detik.
   - Optimasi gambar produk dengan format WebP dan *lazy loading*.
   - Antarmuka *mobile-first* yang mulus di smartphone, tablet, maupun layar desktop.
3. **Kemudahan Pemeliharaan (Maintainability)**:
   - Arsitektur kode bersih berorientasi layanan (*Service Layer Pattern*) untuk Midtrans, Fonnte, Mailer, dan PDF Generator.
   - Konfigurasi kredensial terisolasi penuh pada file `.env`.

---

### 6. Matriks Status Pesanan (Order State Machine)
- `pending`: Menunggu pembayaran via Midtrans Snap.
- `paid`: Pembayaran terverifikasi lunas (Webhook).
- `processing`: Pesanan sedang dipersiapkan dan unit IMEI dialokasikan oleh Admin.
- `shipped`: Pesanan telah diserahkan ke ekspedisi dan nomor resi terbit.
- `completed`: Paket diterima oleh customer.
- `cancelled`: Pesanan dibatalkan (kadaluarsa Midtrans / pembatalan sistem).
