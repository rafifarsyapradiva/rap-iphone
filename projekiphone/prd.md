# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Platform E-Commerce Jual Beli & Tukar Tambah iPhone (Rap-iPhone)

---

### 1. Ringkasan Eksekutif & Visi Produk
**Rap-iPhone** adalah platform e-commerce web modern dan premium yang dirancang khusus untuk transaksi jual-beli serta tukar tambah (*trade-in / buyback*) produk iPhone. Mengadopsi standar layanan dan *user experience* dari *Apple Premium Partner* resmi **Digimap (`https://www.digimap.co.id/`)** dan **iBox** melalui metode **ATM (Amati, Tiru, Modifikasi)**, platform ini memadukan kemudahan alur belanja tanpa ribet, transparansi kondisi unit & garansi, serta estetika visual **Digimap-Inspired Daylight Minimalist & Frosted White UI** (`#FFFFFF` & `#F5F5F7` canvas, tipografi tajam `#1D1D1F`, dan aksen `#0066CC`/`#0071E3`) sebagaimana dirinci pada dokumen `redesign.md`.

Didukung oleh framework **Laravel 12**, basis data **MySQL**, dan template engine **Laravel Blade**, Rap-iPhone dilengkapi integrasi menyeluruh:
- **Google Console Login (OAuth 2.0)** untuk kemudahan masuk dan daftar 1-klik khusus bagi pelanggan (*Customer*).
- **Midtrans Payment Gateway (Snap)** untuk transaksi aman melalui QRIS, Virtual Account, E-Wallet, dan Kartu Kredit.
- **Notifikasi Otomatis WhatsApp (Fonnte API)** dan **Email (SMTP)** untuk *real-time updates* status transaksi.
- **Nota / Invoice PDF Otomatis** dengan desain resmi untuk bukti pembelian yang sah.
- **Manajemen 3 Tingkat Akses (Role) dengan Jalur Login Mandiri Terpisah (*Multi-Portal Role-Isolated Authentication*)**:
  - **Customer (Pelanggan)** melalui jalur publik mandiri (`/login` & `/register` + Google OAuth — **Fase 16**).
  - **Admin Operasional** melalui portal operasional mandiri (`/admin/login` — **Fase 14**).
  - **Pemilik Usaha (Owner)** melalui portal eksekutif mandiri (`/owner/login` — **Fase 15**).
- **Panel Admin & Owner yang rapi dan konsisten (Fase 17–24)**: seluruh halaman operasional (dashboard, produk, pesanan, trade-in, dashboard owner, laporan keuangan, staf, konfigurasi) dirapikan dengan satu Admin Design System (lihat §4.5).

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

### 3. Pengguna, Pemisahan Jalur Login, & Matriks Hak Akses (Role-Based Access)

Setiap tingkatan pengguna (*Customer*, *Admin Operasional*, dan *Owner*) memiliki **jalur masuk (autentikasi/login) mandiri yang terpisah secara ketat (*Strict Role-Isolated Login Portals*)** agar tidak tercampur dalam satu halaman login yang sama:

| Fitur / Modul | Customer (Pelanggan) | Admin Operasional | Pemilik Usaha (Owner) |
|---|:---:|:---:|:---:|
| **Jalur Login Mandiri (Dedicated Portal)** | `/login` (**Fase 16**) | `/admin/login` (**Fase 14**) | `/owner/login` (**Fase 15**) |
| **Registrasi Mandiri (Self-Registration)** | Ya (`/register`) | Tidak (Dikelola Owner) | Tidak (Akun Eksekutif) |
| **Google OAuth 1-Click Login/Register** | Ya (`/auth/google`) | Tidak (Khusus Email & Sandi) | Tidak (Khusus Email & Sandi) |
| **Target Redirect Pasca-Login** | `/` (Storefront / Intended) | `/admin/dashboard` | `/owner/dashboard` |
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

#### 4.1. Modul Pelanggan (Customer Experience — Redesign ATM Digimap & Fase 16)
1. **Jalur Login & Registrasi Mandiri Pelanggan (`Fase 16`)**:
   - **Portal Login Khusus Customer (`/login`)**: Hanya menerima autentikasi dari akun ber-role `customer` (menggunakan email & password terenkripsi Bcrypt). Apabila akun `admin` atau `owner` mencoba masuk melalui halaman login pelanggan, sistem menolak sesi dan mengarahkan ke portal internal terkait (`/admin/login` atau `/owner/login`). Halaman ini bersih dari informasi maupun *demo chip* milik Admin/Owner.
   - **Portal Registrasi Mandiri Customer (`/register`)**: Formulir pendaftaran akun pelanggan baru (`role = 'customer'`) dilengkapi input Nama Lengkap, Email, Nomor WhatsApp aktif (terhubung dengan notifikasi Fonnte), serta konfirmasi Kata Sandi.
   - **Google Console OAuth 1-Klik (`/auth/google`)**: Kemudahan masuk dan daftar instan 1-klik menggunakan akun Google yang dikhususkan secara eksklusif bagi akun pelanggan (`customer`).
2. **Navigasi & Katalog Berstandar Digimap (`digimap.co.id`)**:
   - **Top Promo Announcement Bar & Sticky Daylight Header**: Bar informasi promo aktif (cicilan, gratis ongkir, trade-in) di atas header putih bersih dengan fitur pencarian instan (`⌘K`).
   - **Visual Series Icon Ribbon**: Navigasi ikon seri iPhone horizontal (*iPhone 16 Pro, 16, 15 Pro, 15, 14, 13, Second Like New, Trade-In*) untuk lompatan kategori 1-klik.
   - **Dual-Condition Filter & Clean Product Cards (`#F5F5F7`)**: Filter menurut kondisi Baru (Garansi Resmi iBox/Digimap) vs Second Teruji (dengan indikator Battery Health & Grade), dilengkapi *Interactive Color Swatches*, *Storage Pills*, dan informasi estimasi cicilan/tukar tambah.
3. **Halaman Detail Produk (PDP) v2.0 & Simulasi Harga Interaktif**:
   - **Sticky Studio Gallery & Step-by-Step Configurator**: Pilihan kondisi, warna resmi Apple (*Natural Titanium, Desert Titanium, Midnight, Starlight*, dll), dan kapasitas penyimpanan (128GB–1TB) dengan pembaruan harga instan (*dynamic pricing*).
   - **Inline Trade-In Estimator & Simulasi Cicilan**: Pelanggan dapat melihat simulasi cicilan per bulan serta menghitung estimasi potongan harga langsung di PDP apabila menukar tambah iPhone lama mereka.
   - **Transparency Trust Panel (Khusus Unit Second)**: Widget informasi *Battery Health* (`85%–100%`), nomor IMEI terdaftar Kemenperin, dan sertifikasi lulus QC 32 titik.
   - **Sticky Bottom Purchase Bar**: Bar ringkasan pembelian yang menempel di bawah layar untuk mempercepat konversi belanja.
4. **Alur Checkout Mudah, Omnichannel, & Bebas Ribet**:
   - Checkout langsung (*Buy Now*) atau melalui keranjang belanja (*Cart*).
   - Pilihan metode pemenuhan pesanan: **Pengiriman Kurir Berasuransi** (JNE, SiCepat, J&T) atau **Click & Collect (Ambil Langsung di Gerai Store Rap-iPhone)**.
   - Integrasi popup **Midtrans Snap**: Pembayaran tanpa meninggalkan halaman dengan pilihan metode lengkap (QRIS, BCA VA, Mandiri VA, BRI VA, BNI VA, GoPay, ShopeePay, Kartu Kredit).
5. **Modul Jual / Tukar Tambah (*Trade-In Wizard*)**:
   - Form interaktif bertahap bergaya bersih (Model iPhone lama -> Kapasitas -> Kondisi Fisik Layar/Body -> Kelengkapan & BH).
   - Kalkulasi estimasi harga instan (*instant price quote*).
   - Pilihan: Terima tunai (transfer bank) atau voucher potongan pembelian iPhone baru.
6. **Invoice & Dokumen Transaksi**:
   - Unduh langsung Nota PDF berdesain elegan berstandar *Apple Authorized Partner*.
   - Status garansi, QR Code validasi, dan nomor seri/IMEI tertera pada nota.

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

#### 4.3. Modul Admin Operasional (`Fase 14 — Jalur Login Admin Sendiri`)
1. **Portal Login Mandiri Admin Operasional (`/admin/login` — `Fase 14`)**:
   - Halaman masuk terpisah khusus staf operasional toko (*Rap-iPhone Store Operations Portal*) dengan tema *Digimap Enterprise Light UI*.
   - Validasi *Strict Admin Role Guard*: Hanya akun ber-role `admin` yang diizinkan masuk melalui `/admin/login` (menolak percobaan masuk dari akun `customer` maupun `owner`).
   - Bebas dari tombol Google OAuth publik dan link pendaftaran pelanggan; pasca-login langsung mengarah ke `/admin/dashboard`.
2. **Manajemen Produk & Varian**:
   - Input produk induk (Seri, Deskripsi, Tipe Garansi).
   - Manajemen varian multi-dimensi (Kombinasi Warna + Kapasitas + Stok + IMEI + Harga + Foto Varian).
3. **Manajemen Transaksi & Pesanan**:
   - Monitoring pesanan masuk secara *real-time*.
   - Status otomatis berubah via Midtrans Webhook Callback (*Settlement, Pending, Expire, Cancel*).
   - Form input nomor resi kurir pengiriman (JNE, SiCepat, J&T).
4. **Manajemen Unit Tukar Tambah / Jual iPhone**:
   - Daftar antrean pengajuan dari customer.
   - Form verifikasi fisik unit saat diterima di toko.
   - Penyesuaian penawaran harga final & tombol persetujuan transfer/tukar.

#### 4.4. Modul Pemilik Usaha / Owner Dashboard (`Fase 15 — Jalur Login Owner Sendiri`)
1. **Portal Login Mandiri Eksekutif Owner (`/owner/login` — `Fase 15`)**:
   - Halaman masuk eksklusif bagi Pemilik Usaha (*Rap-iPhone Executive & Financial Portal*) dengan estetika *Apple Executive Daylight & Titanium Accent*.
   - Validasi *Strict Owner Role Guard*: Hanya akun ber-role `owner` yang diizinkan masuk melalui `/owner/login` (menolak tegas percobaan login dari akun `admin` maupun `customer`).
   - Tanpa opsi registrasi mandiri dan tanpa Google OAuth publik; pasca-login langsung mengarah ke `/owner/dashboard`.
2. **Executive Financial Summary**:
   - Total Omzet Penjualan (Harian, Mingguan, Bulanan, Tahunan).
   - Perhitungan Estimasi Laba Kotor & Laba Bersih (Harga Jual dikurangi HPP unit).
   - Ringkasan Arus Kas Masuk (Penjualan) vs Arus Kas Keluar (Pencairan Trade-In).
3. **Performa Bisnis & Produk Terlaris**:
   - Grafik interaktif tren penjualan.
   - Daftar *Top 5 Best-Selling iPhones* dan varian terpopuler.
   - Tingkat konversi transaksi (Pesanan Selesai vs Batal).
4. **Laporan & Ekspor**:
   - Ekspor laporan penjualan ke format PDF dan Excel/CSV.
   - Kontrol akun admin dan staf.

---

#### 4.5. Standar UI/UX Panel Admin & Owner (`Fase 17–24`)
Panel Admin Operasional dan Owner wajib mengikuti **Admin Design System** (`blueprint.md` §6.3) agar tampilan rapi, konsisten, dan nyaman dipandang:
1. **Pola halaman baku**: Page Header (judul, subjudul, aksi utama) → Kartu Metrik (opsional) → Toolbar (tab, pencarian, filter) → Kartu/Tabel konten.
2. **Konsistensi visual**: seluruh halaman memakai kelas/komponen `adm-*` dan token Digimap Daylight (`#FFFFFF`, `#F5F5F7`, `#1D1D1F`, `#0066CC`/`#0071E3`); dilarang memakai kelas tema gelap (`text-white` pada latar terang, `bg-[#161B22]`, `border-white/*`, `text-[#8E8E93]`).
3. **Keterbacaan data**: angka moneter rata kanan dengan `tabular-nums`, header tabel sticky, status memakai badge semantik seragam, kontras teks utama ≥ 4.5:1.
4. **State lengkap**: setiap tabel memiliki state kosong; setiap form menampilkan pesan error per field; aksi destruktif memakai konfirmasi.
5. **Navigasi jujur**: indikator di sidebar/header (jumlah pesanan perlu diproses, trade-in menunggu inspeksi, status Midtrans/Fonnte) menampilkan data nyata, bukan label statis.
6. **Responsivitas**: tanpa scroll horizontal pada lebar 390 px kecuali di dalam kontainer tabel.
7. **Cakupan halaman**: Fase 17 `/admin/dashboard`, Fase 18 `/admin/products`, Fase 19 `/admin/orders`, Fase 20 `/admin/trade-ins`, Fase 21 `/owner/dashboard`, Fase 22 `/owner/financial-report`, Fase 23 `/owner/staff`, Fase 24 `/admin/settings`.

---

### 5. Kebutuhan Non-Fungsional (NFR)
1. **Keamanan (Security & Role-Isolated Auth)**:
   - Sanitasi input dan proteksi terhadap CSRF, XSS, serta SQL Injection (Eloquent ORM).
   - Autentikasi aman password hashing via Bcrypt/Argon2.
   - **Isolasi Jalur Login Berdasarkan Peran (*Multi-Portal Role Isolation — Fase 14, 15, & 16*)**: Pemisahan penuh endpoint, *rate limiter*, dan antarmuka login antara Admin (`/admin/login`), Owner (`/owner/login`), serta Customer (`/login` & `/register`), disertai proteksi penolakan lintas-jalur (*cross-portal login rejection*) dan pengalihan *guest* kontekstual sesuai prefix URL yang diakses.
   - Verifikasi SHA512 Signature Hash pada Webhook Midtrans untuk mencegah manipulasi status transaksi.
2. **Performa, Desain UI/UX, & Responsivitas**:
   - Waktu muat halaman pertama di bawah 2 detik.
   - Optimasi gambar produk dengan format WebP dan *lazy loading*.
   - Standar visual **Digimap-Inspired Daylight Minimalist & Frosted White UI** (`redesign.md`): menggunakan latar terang ber-kontras tinggi (`#FFFFFF` & `#F5F5F7`), tipografi `#1D1D1F`, dan *sticky purchase bar* untuk kenyamanan baca di bawah kondisi pencahayaan apa pun.
   - Antarmuka *mobile-first* yang mulus di smartphone, tablet, maupun layar desktop (baik untuk Portal Customer maupun Dashboard Enterprise Admin & Owner).
   - **Panel Admin & Owner (Fase 17–24)** wajib memakai Admin Design System (`adm-*`) sesuai §4.5 dan tidak boleh memakai kelas tema gelap.
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
