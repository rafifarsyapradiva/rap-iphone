# UI/UX REDESIGN SPECIFICATION (v2.0) — MODE AMATI, TIRU, MODIFIKASI (ATM)
## Platform E-Commerce Jual Beli & Tukar Tambah iPhone (Rap-iPhone)
### Inspirasi Utama: Apple Premium Partner — Digimap Indonesia (`https://www.digimap.co.id/`)

---

### 1. Latar Belakang & Tujuan Redesign

Pada versi awal (`design.md` v1.0), **Rap-iPhone** menggunakan pendekatan visual *Dark Deep-Space Glassmorphism* (`#0B0C10`). Meskipun tampak futuristik, riset pengalaman pengguna (*User Experience*) pada e-commerce retail resmi Apple di Indonesia—khususnya **Digimap (`digimap.co.id`)**—menunjukkan bahwa tingkat kepercayaan konsumen (*buyer trust*), kejelasan warna fisik unit iPhone, dan keterbacaan informasi harga/cicilan jauh lebih optimal pada antarmuka **Clean White & Daylight Minimalist**.

Dokumen **`redesign.md`** ini menjadi acuan utama transformasi antarmuka **Rap-iPhone v2.0** menggunakan pendekatan **ATM (Amati, Tiru, Modifikasi)** terhadap situs resmi **Digimap Indonesia**, sekaligus mempertahankan keunggulan unik Rap-iPhone (penjualan unit Baru BNIB + Second Like New Teruji + Program Tukar Tambah Instan 15 Menit).

---

### 2. Metodologi ATM (Amati, Tiru, Modifikasi) dari `digimap.co.id`

#### 2.1. AMATI (Observasi Anatomi Desain & UX Digimap)
Berdasarkan bedah struktur visual dan alur belanja pada `https://www.digimap.co.id/`, terdapat 6 karakteristik kunci yang membangun kesan *Authorized Apple Retail*:
1. **Dominasi Warna Terang & Whitespace Lega (*Daylight Retail Canvas*)**:
   - Latar belakang utama putih bersih (`#FFFFFF`) dipadukan dengan permukaan kartu produk abu-abu terang khas Apple (`#F5F5F7`) dan teks hitam pekat (`#1D1D1F`).
   - Foto produk iPhone tampil sangat kontras, jujur, dan menonjolkan warna asli bodi titanium/aluminium tanpa distorsi efek lampu neon gelap.
2. **Top Promo Announcement Bar (Ticker Informasi Atas)**:
   - Bar tipis di bagian paling atas halaman yang menampilkan info kampanye aktif (misal: *"Cicilan 0% hingga 24 Bulan"*, *"Gratis Ongkir & Asuransi Pengiriman ke Seluruh Indonesia"*, atau *"Program Trade-In Resmi"*).
3. **Header Minimalis & Navigasi Berbasis Kategori Produk**:
   - Header putih bersih dengan garis batas bawah sangat halus (`1px solid #E5E5EA`), memuat logo di kiri, menu seri/kategori di tengah, serta pencarian instan, lokasi gerai (*Store Locator*), keranjang, dan akun di kanan.
4. **Visual Category / Series Icon Ribbon**:
   - Baris ikon visual horizontal di bawah header/hero yang memudahkan pelanggan melompat langsung ke seri spesifik (*iPhone 16 Pro, iPhone 16, iPhone 15, iPhone 14, iPhone 13, Aksesoris*).
5. **Hero Banner Kampanye & Bento Promotional Grid**:
   - Banner utama lebar (*edge-to-edge / wide rounded container*) dengan visual produk ukuran besar, tipografi tegas, badge *"Baru"* atau *"Tersedia Sekarang"*, serta grid kartu promo perbankan/layanan di bawahnya.
6. **Product Detail Page (PDP) Berorientasi Konversi & Omnichannel**:
   - Menampilkan harga retail utama berdampingan dengan informasi **Simulasi Cicilan per Bulan** (*"Mulai dari Rp .../bln"*).
   - Indikator ketersediaan stok untuk pengiriman kurir maupun **Click & Collect (Ambil di Gerai Toko)**.

#### 2.2. TIRU (Elemen Standar yang Diadopsi ke Rap-iPhone)
Rap-iPhone mengadopsi pola terbaik dari Digimap untuk menjamin familiaritas dan rasa aman pelanggan:
- **Palet Warna Daylight Minimalist**: Menggunakan kombinasi `#FFFFFF` (Main Canvas), `#F5F5F7` (Card & Section Surface), `#1D1D1F` (Obsidian Text), dan `#0066CC` / `#0071E3` (Apple Retail Blue).
- **Struktur Navigasi 3 Lapis**: *Top Promo Bar* $\rightarrow$ *Sticky Daylight Frosted Header* $\rightarrow$ *Visual iPhone Series Ribbon*.
- **Gaya Kartu Produk Digimap**: Kontainer kartu `#F5F5F7` tanpa border tebal, sudut membulat `18px–20px`, badge status di kiri atas, titik warna (*color swatches*) di bawah gambar, nama produk tegas, harga utama, dan teks sub-harga (*estimasi cicilan / tukar tambah*).
- **Layout PDP 2 Kolom dengan Galeri Sticky**: Kolom kiri menampilkan galeri foto resolusi tinggi yang tetap menempel (*sticky*) saat pengguna menggulir pilihan konfigurasi di kolom kanan.
- **Sticky Bottom Purchase Bar**: Bar ringkasan pembelian yang menempel di bagian bawah layar saat pengguna membaca spesifikasi di halaman produk.

#### 2.3. MODIFIKASI (Inovasi Khusus Ekosistem Rap-iPhone)
Berbeda dengan Digimap yang hanya menjual unit baru (*Brand New in Box*), **Rap-iPhone** memiliki model bisnis hibrida (**BNIB Garansi Resmi + Second Like New Teruji + Trade-In Instan**). Oleh karena itu, desain Digimap dimodifikasi dengan inovasi berikut:

| Aspek Desain | Standar Digimap (`digimap.co.id`) | Modifikasi & Inovasi di **Rap-iPhone v2.0** |
|---|---|---|
| **Efek Permukaan UI** | Flat White & Gray | **Daylight Frosted Glass**: Lapisan kaca putih susu semi-transparan (`rgba(255, 255, 255, 0.84)` + `backdrop-filter: blur(20px)`) pada Sticky Header, Floating Buy Bar, dan Modal. |
| **Filter & Kondisi Produk** | Hanya Unit Baru (BNIB) | **Dual-Condition Segmented Control**: Tab elegan ala Apple Switcher untuk memfilter **[Semua]**, **[Unit Baru Garansi Resmi]**, dan **[Second Like New Teruji]**. |
| **Kartu Produk & PDP Unit Second** | Tidak tersedia | **Transparency Trust Panel**: Menampilkan *Battery Health Meter* (`85%–100%`), *Grade Fisik (`Grade A+ / A`)*, *Status IMEI Terdaftar Kemenperin*, dan *Lulus QC 32 Titik* dalam box putih bersih bergaya Apple Spec Card. |
| **Simulasi Harga di PDP** | Simulasi Cicilan Bank | **Dual Price Estimator (Cicilan + Potongan Trade-In)**: Selain tab simulasi metode bayar Midtrans, pelanggan dapat memilih model iPhone lamanya langsung di PDP untuk melihat **Harga Akhir Setelah Tukar Tambah**. |
| **Layanan Omnichannel** | Click & Collect Gerai MAP | **Click & Collect + Inspeksi Tukar Tambah Toko**: Opsi *"Kirim via Ekspedisi Berasuransi"* atau *"Ambil & Cek Unit Langsung di Store Rap-iPhone"*. |
| **Dokumen & Pasca-Beli** | Email Invoice Standar | **Nota PDF Resmi Ber-QR Code + Notifikasi WhatsApp Otomatis (Fonnte)** untuk status pembayaran, alokasi IMEI, dan resi kurir. |

---

### 3. Sistem Palet Warna v2.0 (Digimap Daylight Palette)

#### 3.1. Token Warna Utama (Light & Frosted White Mode)
| Token CSS | Nama Peran | Nilai Hex / RGBA | Penggunaan Utama |
|---|---|---|---|
| `--dm-canvas` | **Pure White Canvas** | `#FFFFFF` | Latar belakang utama seluruh halaman |
| `--dm-surface` | **Apple Warm Gray** | `#F5F5F7` | Latar kartu produk, kotak spesifikasi, banner sekunder, input field |
| `--dm-surface-hover` | **Elevated Gray Surface** | `#EBEBED` | State hover pada kartu produk dan tombol sekunder |
| `--dm-glass-white` | **Daylight Frosted Glass** | `rgba(255, 255, 255, 0.84)` | Sticky header, sticky bottom bar, modal overlay (`backdrop-filter: blur(20px)`) |
| `--dm-border` | **Hairline Divider** | `#D2D2D7` | Border kartu aktif, input form, garis pemisah tabel |
| `--dm-border-subtle` | **Subtle Section Line** | `#E8E8ED` | Garis pemisah tipis antar seksi dan header |
| `--dm-text-primary` | **Obsidian Black** | `#1D1D1F` | Judul utama, nama produk, harga, tombol utama gelap |
| `--dm-text-secondary`| **Neutral Slate Gray** | `#6E6E73` | Deskripsi produk, sub-judul, navigasi non-aktif |
| `--dm-text-muted` | **Muted Caption** | `#86868B` | Catatan kaki, label syarat & ketentuan, timestamp |
| `--dm-blue` | **Apple Retail Blue** | `#0066CC` | Tautan interaktif, border pilihan varian aktif, indikator fokus |
| `--dm-blue-cta` | **Primary Action Blue** | `#0071E3` | Tombol utama *"Beli Sekarang"*, badge aktif, highlight utama |
| `--dm-dark-cta` | **Digimap Pill Black** | `#1D1D1F` | Tombol sekunder kontras tinggi, tombol filter aktif |

#### 3.2. Token Warna Status & Kepercayaan (Semantic Badges)
| Kategori Status | Warna Teks / Ikon | Warna Latar Badge | Penerapan pada UI |
|---|---|---|---|
| **Garansi Resmi / BNIB** | `#0066CC` | `#E8F2FF` | Badge `[Resmi iBox / Digimap]`, `[100% Original]` |
| **Ready Stock / BH Sehat** | `#008040` | `#E8F8EE` | Badge `[Stok Tersedia]`, `[Battery Health 95%]`, `[Lunas]` |
| **Promo / Rilis Baru** | `#BF4800` | `#FFF2EB` | Label `"Baru"`, `"Promo Trade-In"`, `"Hemat Rp 1,5 Jt"` |
| **Stok Terbatas / Pending** | `#B25000` | `#FFF8E6` | Badge `[Sisa 1 Unit]`, `[Menunggu Pembayaran]` |
| **Diskon / Batal** | `#D70015` | `#FFEBEA` | Badge potongan harga coret, status pesanan dibatalkan |

#### 3.3. Token Warna Fisik Resmi iPhone (Color Swatch Dots)
Tetap mempertahankan akurasi warna bodi iPhone dengan tambahan ring border halus (`1px solid rgba(0, 0, 0, 0.15)`) agar warna terang seperti *White Titanium* dan *Starlight* tetap terlihat jelas di atas latar putih:
- **Natural Titanium**: `#9A958E`
- **Desert Titanium**: `#C5B49F`
- **White Titanium / Starlight**: `#F2F1EC`
- **Black Titanium / Midnight**: `#2D2F33`
- **Ultramarine**: `#496495`
- **Teal**: `#799896`
- **Pink**: `#E8C4C6`

---

### 4. Tipografi & Sistem Grid (Digimap Editorial Scale)

- **Font Family**: `'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', sans-serif`
- **Karakter Tipografi**: Bersih, *tight tracking* pada judul besar, dan kontras bobot yang jelas antara nama seri dengan harga.
- **Skala Hierarki Teks**:
  - **Hero Banner Headline**: `40px – 52px` (`font-weight: 700`, `line-height: 1.08`, `letter-spacing: -0.025em`, warna `#1D1D1F`)
  - **Section Heading**: `28px – 34px` (`font-weight: 700`, `letter-spacing: -0.02em`, warna `#1D1D1F`)
  - **Product Card Title**: `17px – 19px` (`font-weight: 600`, `line-height: 1.35`, warna `#1D1D1F`)
  - **PDP Main Price**: `26px – 30px` (`font-weight: 700`, warna `#1D1D1F`)
  - **Installment / Trade-In Subtext**: `13px – 14px` (`font-weight: 500`, warna `#6E6E73`)
  - **Micro Eyebrow / Tag**: `11px – 12px` (`font-weight: 600`, ` uppercase`, `letter-spacing: 0.06em`, warna `#BF4800` atau `#0066CC`)
- **Container & Grid**:
  - Lebar maksimum kontainer utama: `1280px` (`max-w-7xl`) dengan padding horizontal adaptif (`16px` mobile, `24px` tablet, `32px` desktop).
  - Grid Katalog: 4 kolom pada Desktop (`lg:grid-cols-4`), 2 kolom pada Tablet/Mobile (`grid-cols-2`), jarak antar kartu `20px–24px` (`gap-5` / `gap-6`).

---

### 5. Spesifikasi Komponen UI Utama (Redesign v2.0)

#### 5.1. Top Promo Announcement Bar & Sticky Daylight Header
```
+--------------------------------------------------------------------------------------------------+
| [Dark Bar #1D1D1F] Gratis Ongkir Berasuransi • Tukar Tambah 15 Menit • Cicilan & QRIS Midtrans   |
+--------------------------------------------------------------------------------------------------+
| [Logo Rap-iPhone]   iPhone Baru  |  Second Like New  |  Tukar Tambah  |  Cek IMEI   [Cari] [Bag] |
+--------------------------------------------------------------------------------------------------+
| [iPhone 16 Pro]  [iPhone 16]  [iPhone 15 Pro]  [iPhone 15]  [iPhone 14]  [iPhone 13]  [Trade-In] |
+--------------------------------------------------------------------------------------------------+
```
1. **Top Promo Bar**:
   - Latar `#1D1D1F` atau `#F5F5F7` dengan teks `12px` terpusat, memuat informasi promo utama dan tautan cepat ke halaman *Tukar Tambah* atau *Lacak Pesanan*.
2. **Sticky Daylight Header**:
   - Latar `rgba(255, 255, 255, 0.85)` dengan `backdrop-filter: blur(20px)` dan `border-bottom: 1px solid #E8E8ED`.
   - **Kiri**: Wordmark **Rap-iPhone** tegas berwarna `#1D1D1F` dengan badge kecil *"Authorized Style Partner"*.
   - **Tengah**: Tautan navigasi bersih (`#1D1D1F` saat aktif, `#6E6E73` saat normal): **Katalog iPhone**, **Unit Baru (BNIB)**, **Second Like New**, **Tukar Tambah**, **Cek Garansi & IMEI**.
   - **Kanan**:
     - Tombol *Search Pill* berlatar `#F5F5F7` (`"Cari iPhone 16 Pro, 256GB..."` + pintasan `⌘K`).
     - Indikator **Lokasi Toko / Ambil di Gerai**.
     - Ikon *Shopping Bag* dengan badge jumlah item berwarna `#0071E3` atau `#1D1D1F`.
     - Menu Akun Pelanggan / Tombol Login Google cepat.
3. **Visual Series Icon Ribbon**:
   - Baris navigasi bergambar siluet seri iPhone di bagian atas halaman Beranda dan Katalog agar pengguna dapat memfilter seri dalam 1 klik seperti di `digimap.co.id`.

#### 5.2. Hero Banner Kampanye & Bento Promo Grid (Beranda)
- **Hero Showcase**:
  - Kontainer penuh atau *wide rounded banner* (`border-radius: 28px`) dengan latar bersih `#F5F5F7` (atau panel kontras Titanium khusus seri *Pro*).
  - Menampilkan label oranye `#BF4800` (`"TERSEDIA SEKARANG"`), judul besar **"iPhone 16 Pro"**, sub-judul *"Desain Titanium. Kontrol Kamera. A18 Pro."*, info harga *"Mulai dari Rp 18.499.000 atau tukar tambah hemat hingga Rp 12 Juta"*.
  - Dua tombol CTA bergaya Apple/Digimap:
    - Primary Pill (`#0071E3` teks putih): **"Beli Sekarang"**
    - Secondary Pill (`border: 1px solid #1D1D1F` teks `#1D1D1F`): **"Tukar Tambah iPhone Lama"**
- **Bento Service & Promo Highlights (4 Kartu di Bawah Hero)**:
  1. **Garansi Resmi Indonesia**: Unit BNIB bersegel resmi (iBox / Digimap / TAM).
  2. **Certified Second Like New**: Lulus inspeksi 32 titik, Battery Health > 85%, IMEI terdaftar resmi.
  3. **Tukar Tambah 15 Menit**: Kalkulator taksiran instan online, bisa langsung potong harga beli.
  4. **Pembayaran Aman & Fleksibel**: QRIS, Virtual Account Semua Bank, Kartu Kredit & E-Wallet via Midtrans, atau Ambil di Toko (*Click & Collect*).

#### 5.3. Kartu Produk v2.0 (Digimap Clean Product Card)
Setiap kartu produk pada halaman Beranda dan Katalog dirancang bersih, informatif, dan bebas sesak:
- **Permukaan Kartu**: Background `#F5F5F7`, `border-radius: 20px`, `padding: 24px`, transisi halus saat di-hover (`background: #EFEFF4`, `transform: translateY(-4px)`).
- **Baris Atas (Badges)**:
  - Sudut kiri atas: Pill badge status kondisi, misal `[Baru • Resmi]` (Biru muda `#E8F2FF` teks `#0066CC`) atau `[Second • Grade A+]` (Hijau muda `#E8F8EE` teks `#008040`).
  - Sudut kanan atas (khusus Second): Badge kecil `[BH 96%]`.
- **Area Gambar**: Rasio `1:1` dengan latar bersih, menampilkan foto studio unit iPhone yang tajam.
- **Color Swatches**: Deretan titik warna (`14px x 14px`) di bawah gambar dengan ring seleksi aktif.
- **Informasi Produk**:
  - Tag seri kecil (`11px`, `#6E6E73`) $\rightarrow$ Nama Produk (`18px` Semi-Bold `#1D1D1F`).
  - Pill pilihan kapasitas ringkas (`128GB`, `256GB`, `512GB`).
  - **Harga Utama**: `19px` Bold `#1D1D1F` (misal: `Rp 18.499.000`).
  - **Sub-info Finansial**: `12px` `#6E6E73` (*"Cicilan mulai Rp 770.791/bln"* atau *"Tukar tambah mulai Rp 9.500.000"*).
- **Aksi Cepat**: Tombol penuh lebar berlatar `#FFFFFF` dengan border `#D2D2D7` yang berubah menjadi `#1D1D1F` (teks putih) saat di-hover: **"Pilih Varian"** / **"Beli"**.

#### 5.4. Halaman Rincian Produk (Product Detail Page — PDP Ala Digimap + Modifikasi Rap-iPhone)
Mengadopsi tata letak 2 kolom presisi seperti `digimap.co.id`:
1. **Kolom Kiri (Sticky Studio Gallery — Lebar 55%)**:
   - Bingkai gambar utama besar di atas permukaan `#F5F5F7` (`border-radius: 24px`).
   - Baris thumbnail multi-sudut di bawahnya.
   - Bar keunggulan singkat di bawah galeri: *Gratis Pengiriman Berasuransi*, *Garansi Resmi / Toko Terjamin*, *Ambil di Gerai Tersedia*.
2. **Kolom Kanan (Step-by-Step Configurator — Lebar 45%)**:
   - **Header Produk**: Badge Kondisi (`Baru Garansi Resmi` / `Second Like New`), Judul Seri iPhone (`32px Bold #1D1D1F`), Harga Dinamis yang langsung berubah saat kapasitas diklik, serta tautan *"Lihat simulasi cicilan & metode bayar"*.
   - **Step 1 — Pilih Warna (*Finish*)**: Menampilkan nama warna aktif (misal: *Warna — Desert Titanium*) dan tombol lingkaran warna berdiameter `36px` dengan ring fokus biru `#0066CC`.
   - **Step 2 — Pilih Kapasitas Penyimpanan (*Storage*)**: Grid kartu kotak 2 kolom (`border-radius: 14px`, border `2px solid #0066CC` saat terpilih, `1px solid #D2D2D7` saat tidak terpilih) yang menampilkan angka kapasitas besar (`256GB`) di kiri dan harga spesifik di kanan.
   - **Step 3 — Panel Transparansi Unit (Khusus Produk Second)**:
     - Kotak `#F5F5F7` dengan rincian *Battery Health Aktual*, *Kondisi Fisik Grade*, *Kelengkapan Kotak*, dan *Status Sinyal IMEI All Operator*.
   - **Step 4 — Widget Tukar Tambah Instan (*Modifikasi Khas Rap-iPhone*)**:
     - Kartu interaktif *"Punya iPhone lama? Tukar tambah sekarang"*: pengguna dapat memilih seri iPhone lama dari dropdown cepat untuk melihat estimasi potongan harga langsung pada PDP, atau lanjut ke halaman *Trade-In Wizard* lengkap.
   - **Step 5 — Informasi Pengiriman & Ketersediaan Stok (*Omnichannel*)**:
     - Menampilkan status stok varian (`Tersedia — Siap Kirim Hari Ini`) dan opsi **Kurir Ekspedisi (JNE/SiCepat/J&T)** atau **Ambil di Toko (Click & Collect Store Rap-iPhone)**.
   - **Tombol Aksi Utama**:
     - Tombol penuh `#0071E3` (teks putih, tinggi `52px`, `border-radius: 14px`): **"Beli Sekarang"**
     - Tombol sekunder `#1D1D1F` atau outline `#1D1D1F`: **" + Tambah ke Keranjang"**
3. **Sticky Bottom Purchase Bar**:
   - Muncul otomatis saat pengguna menggulir ke tabel spesifikasi bawah: menampilkan foto mini varian terpilih, nama + warna + kapasitas, harga total, dan tombol cepat **"Beli Sekarang"**.

#### 5.5. Halaman Pendukung Pelanggan (Clean Daylight Experience)
- **Keranjang Belanja (`cart.blade.php`) & Checkout (`checkout.blade.php`)**:
  - Latar putih bersih dengan kartu ringkasan pesanan di sebelah kanan berlatar `#F5F5F7`.
  - Pilihan pengiriman mendukung **Kurir Ekspedisi** maupun **Ambil di Gerai (Click & Collect)**.
  - Integrasi popup **Midtrans Snap** dengan backdrop kaca putih terang yang bersih.
- **Wizard Tukar Tambah (`trade-in.blade.php`)**:
  - Tampilan kuesioner 4 langkah bergaya *Apple Trade-In / Digimap Trade-In* dengan kartu pilihan yang terang, indikator langkah (*stepper*) berwarna `#0066CC`, dan kartu hasil estimasi harga yang elegan.
- **Cek Garansi & IMEI (`utilities.blade.php`)**:
  - Kotak pencarian bersih untuk memeriksa status garansi unit dan verifikasi keaslian nota/IMEI.

#### 5.6. Panel Operasional Admin & Dashboard Owner (Digimap Enterprise Light UI)
Seluruh halaman internal (`layouts/admin.blade.php`, dashboard Admin, dan dashboard Owner) ikut diselaraskan ke **Digimap Enterprise Clean Light Theme**:
- **Sidebar Navigasi**: Latar `#F5F5F7` dengan garis kanan `1px solid #E5E5EA`, item menu aktif berlatar `#1D1D1F` (teks putih) atau `#E8F2FF` (teks `#0066CC`).
- **Kartu Metrik & Tabel Data**: Latar `#FFFFFF` dengan border halus `#E5E5EA` dan shadow lembut (`0 2px 10px rgba(0, 0, 0, 0.03)`), membuat pembacaan angka omzet, HPP, laba bersih, nomor IMEI, dan status pesanan jauh lebih nyaman untuk operasional harian.

---

### 6. Pemetaan Implementasi File (CSS & Blade Views)

Untuk merealisasikan desain dalam dokumen `redesign.md` ini ke dalam kode aplikasi Laravel 12 Rap-iPhone, berikut adalah pemetaan komponen yang ditransformasi:

1. **Fondasi Stylesheet**:
   - `resources/css/glassmorphism.css` & `resources/css/app.css`: Mengubah variabel root CSS dari palet gelap ke **Digimap Daylight Palette** (`#FFFFFF`, `#F5F5F7`, `#1D1D1F`, `#0066CC`), menyesuaikan class `.glass-card`, `.glass-navbar`, `.glass-button`, `.glass-input`, dan `.glass-modal` agar merender tampilan *Clean White & Daylight Frosted UI*.
2. **Master Layouts & Reusable Components**:
   - `resources/views/layouts/customer.blade.php`: Menambahkan *Top Promo Announcement Bar*, memperbarui *Sticky Daylight Header*, dan merombak footer menjadi bergaya retail resmi Digimap.
   - `resources/views/layouts/admin.blade.php`: Menyelaraskan sidebar dan topbar Admin/Owner ke tema terang bersih.
   - `resources/views/components/product-card.blade.php`, `glass-card.blade.php`, `glass-button.blade.php`, `order-badge.blade.php`, `battery-meter.blade.php`.
3. **Halaman Pelanggan (Customer Portal)**:
   - `resources/views/customer/home.blade.php`: Menambahkan *Visual Series Icon Ribbon*, *Hero Showcase Digimap Style*, *Bento Promo Grid*, dan seksi katalog bersih.
   - `resources/views/customer/catalog.blade.php`: Filter sidebar terang, pill kondisi, dan grid kartu produk v2.0.
   - `resources/views/customer/pdp.blade.php`: Galeri sticky `#F5F5F7`, konfigurator bertahap, kalkulator potongan trade-in inline, simulasi cicilan, info *Click & Collect*, dan *Sticky Bottom Purchase Bar*.
   - `resources/views/customer/cart.blade.php`, `checkout.blade.php`, `checkout-success.blade.php`, `orders.blade.php`, `trade-in.blade.php`, `trade-in-detail.blade.php`, `utilities.blade.php`.
4. **Halaman Autentikasi & Panel Internal**:
   - `resources/views/auth/login.blade.php` & `register.blade.php`.
   - Seluruh view di `resources/views/admin/**` dan `resources/views/owner/**`.

