# UI/UX DESIGN SYSTEM & GLASSMORPHISM SPECIFICATION
## Platform E-Commerce Jual Beli iPhone (Rap-iPhone)
### Terinspirasi dari Standar Retail Resmi Apple (iBox & Digimap)

---

### 1. Filosofi Desain & Karakter Visual

Desain Rap-iPhone menggabungkan dua pilar utama:
1. **Apple Clean Minimalism**: Tata letak yang rapi, *whitespace* lega, tipografi berbobot tegas, dan penekanan penuh pada detail fisik produk iPhone (warna titanium asli, modul kamera, layar Retina).
2. **Modern Glassmorphism (Frosted Glass Effect)**:
   - Penggunaan lapisan kaca buram semi-transparan (`backdrop-filter: blur(20px)`).
   - Garis tepi tipis berkilau (*subtle borders*) menyerupai pantulan cahaya pada sudut kaca (`border: 1px solid rgba(255, 255, 255, 0.15)`).
   - Efek pencahayaan latar belakang (*ambient glows & gradient orbs*) yang memberikan kedalaman ruang 3D tanpa membuat mata lelah.

---

### 2. Palet Warna (Color System)

#### 2.1. Dark Glass Mode (Default / Signature Look)
| Peran Warna | Hex / RGBA | Penerapan |
|---|---|---|
| **Deep Space Canvas** | `#0B0C10` | Latar belakang dasar halaman |
| **Glass Card Surface** | `rgba(22, 27, 34, 0.65)` | Kartu produk, kontainer formulir, modal pop-up |
| **Glass Card Elevated**| `rgba(33, 38, 45, 0.85)` | Floating navigation, dropdown menu, active cards |
| **Glass Border** | `rgba(255, 255, 255, 0.12)` | Border pemisah elemen kaca |
| **Apple Electric Blue**| `#0071E3` | Tombol CTA utama, status aktif, link navigasi |
| **Apple Blue Glow** | `rgba(0, 113, 227, 0.35)` | Efek glow pada tombol checkout & banner promo |
| **Titanium Silver** | `#E5E5EA` | Tipografi judul utama, label harga besar |
| **Muted Slate** | `#8E8E93` | Teks sekunder, deskripsi spesifikasi |
| **Success Emerald** | `#34C759` | Status lunas, baterai sehat (BH > 90%), ready stock |
| **Warning Amber** | `#FF9F0A` | Sisa stok menipis, verifikasi unit pending |

#### 2.2. Warna Representasi Varian Asli iPhone (Color Dot Tokens)
- **Natural Titanium**: `#9A958E`
- **Desert Titanium**: `#C5B49F`
- **White Titanium / Starlight**: `#F2F1EC`
- **Black Titanium / Midnight**: `#2D2F33`
- **Ultramarine Blue (iPhone 16)**: `#496495`
- **Teal Green**: `#799896`
- **Pink**: `#E8C4C6`

---

### 3. Tipografi & Hierarki Font

- **Font Family**: `-apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Inter", sans-serif`
- **Skala Ukuran**:
  - `Display / Hero Title`: 48px - 56px (Bold / 700, letter-spacing: -0.02em)
  - `Section Title`: 32px - 36px (Semi-Bold / 600)
  - `Card Header / Product Name`: 20px - 24px (Semi-Bold / 600)
  - `Price Display`: 22px - 28px (Bold / 700, warna Titanium / White)
  - `Body Text`: 15px - 16px (Regular / 400, line-height: 1.6)
  - `Badges & Captions`: 12px - 13px (Medium / 500, uppercase tracked)

---

### 4. Spesifikasi Komponen UI Utama

#### 4.1. Navbar Frosted Glass (Floating Navigation)
- Menempel di bagian atas layar (*sticky header*).
- Style: `backdrop-filter: blur(25px); background: rgba(11, 12, 16, 0.75); border-bottom: 1px solid rgba(255, 255, 255, 0.08);`
- Elemen:
  - Logo Rap-iPhone bernuansa Apple ikonik.
  - Link Menu: **Katalog iPhone**, **Tukar Tambah / Jual**, **Cek Garansi & IMEI**, **Lacak Pesanan**.
  - Search bar interaktif dengan hasil pencarian instan (*instant live search*).
  - Ikon Keranjang Belanja (*Floating cart badge* dengan animasi angka saat bertambah).
  - Ikon Profil Pengguna / Tombol Google Login cepat.

#### 4.2. Hero Showcase (Ala Peluncuran Produk iBox)
- Banner visual dinamis dengan orbs gradien bercahaya halus di belakang unit iPhone.
- Judul besar: *"Inovasi Terdepan. Garansi Terpercaya."*
- Tombol aksi ganda (*Dual CTA Button*):
  - Tombol 1 (Primary Glass Blue): *"Beli iPhone Sekarang"* (menuju katalog).
  - Tombol 2 (Secondary Glass Outline): *"Tukar Tambah Unit Lama"* (menuju Trade-In Wizard).
- Tiga pilar keunggulan toko dengan ikon kaca:
  1. *100% Produk Original Garansi Resmi*.
  2. *Gratis Pengiriman & Asuransi Penuh*.
  3. *Tukar Tambah Transparan Dalam 15 Menit*.

#### 4.3. Kartu Produk Interaktif (Interactive Glass Card)
- Card Container: Sudut membulat modern (`border-radius: 20px`), background kaca semi-transparan, efek bayangan lembut (`box-shadow: 0 10px 30px rgba(0,0,0,0.3)`).
- Efek Hover: Mengangkat halus (`transform: translateY(-6px)`), border kaca menjadi lebih terang (`border-color: rgba(255, 255, 255, 0.3)`).
- Komponen di dalam Kartu:
  - **Badge Status**: Contoh: `[Garansi Resmi iBox]` (Biru) atau `[Second Like New - Grade A+]` (Hijau).
  - **Gambar Produk Resolusi Tinggi**: Tampilan unit iPhone dari sudut terbaik.
  - **Color Selector Dots**: Titik warna interaktif; saat diklik mengganti gambar varian iPhone secara langsung.
  - **Storage Chips**: Pilihan kapasitas ringkas (128GB, 256GB, 512GB).
  - **Harga Dinamis**: Harga otomatis berubah saat varian kapasitas dipilih.
  - **Tombol Cepat**: Ikon keranjang & tombol *"Lihat Detail"*.

#### 4.4. Halaman Rincian Produk (Product Detail Page - PDP Ala iBox)
Mengikuti alur konversi tinggi yang tidak berbelit-belit:
1. **Kolom Kiri**: Galeri foto multi-sudut dengan kaca pembesar (*image zoom*) dan preview warna terpilih.
2. **Kolom Kanan (Configuration Panel)**:
   - Seri & Nama Produk.
   - Pilihan Kondisi: Tab toggle antara **Unit Baru (BNIB)** dan **Second Berkualitas**.
   - Jika Second: Muncul panel widget kesehatan baterai (*Battery Health Meter* 85% - 100%), nomor seri/IMEI transparan, dan kelengkapan box.
   - Pilihan Warna (*Finish Color*): Pilihan tombol bulat dengan nama resmi Apple.
   - Pilihan Kapasitas (*Storage*): Grid tombol kotak minimalis dengan info harga per kapasitas.
   - Simulasi Cicilan atau Estimasi Tukar Tambah.
   - Tombol Aksi: *"Beli Sekarang (Instant Checkout)"* dan *"Tambah ke Keranjang"*.
3. **Bagian Bawah**: Tabel spesifikasi teknis lengkap (Chipset Bionic/Apple Silicon, Kamera, Layar Super Retina XDR, Daya Tahan Baterai).

#### 4.5. Wizard Jual / Tukar Tambah iPhone (Trade-In Flow)
Alur 4 langkah mudah tanpa ribet:
- **Langkah 1**: Pilih Model iPhone lama pelanggan (dropdown/grid seri dari iPhone 11 hingga 15 Pro Max).
- **Langkah 2**: Pilih Kapasitas Penyimpanan.
- **Langkah 3**: Kuesioner Kondisi Fisik & Fungsi (Layar LCD, Face ID, TrueTone, Kondisi Bodi, Persentase Baterai).
- **Langkah 4**: Muncul **Estimasi Harga Taksiran** instan.
  - Opsi A: Cairkan sebagai dana tunai (input rekening bank customer).
  - Opsi B: Gunakan sebagai kupon potongan belanja iPhone baru di Rap-iPhone.

#### 4.6. Checkout & Pop-up Pembayaran Midtrans Snap
- Halaman checkout bersih tanpa distraksi (*Distraction-free Single Page Checkout*).
- Form input alamat ringkas: Nama, No WhatsApp (untuk konfirmasi Fonnte), Alamat Lengkap, Pilihan Kurir.
- Kolom ringkasan pesanan kaca: Menampilkan rincian unit, proteksi asuransi, ongkos kirim, dan grand total.
- Saat tombol *"Bayar Sekarang"* ditekan, popup modal **Midtrans Snap** muncul dengan efek kaca *backdrop overlay*.

#### 4.7. Template Nota / Faktur PDF Resmi
- Ukuran standar A4, layout formal bergaya retail modern Apple.
- Header: Logo Rap-iPhone, alamat fisik toko, nomor kontak resmi WhatsApp dan email layanan pelanggan.
- Tabel Rincian: No, Seri iPhone, Varian Warna & Kapasitas, Nomor IMEI Unit, Status Garansi, Harga, Subtotal.
- Kotak Verifikasi: QR Code untuk validasi keaslian nota di sistem Rap-iPhone.
- Footer: Syarat klaim garansi toko (misal: 7 hari ganti unit baru bila cacat pabrik) dan stempel digital toko.

#### 4.8. Dashboard Khusus Admin & Pemilik Usaha (Owner)
- Sidebar kaca navigasi dengan ikon minimalis.
- Kartu Metrik Frosted Glass:
  - Kartu Omzet Harian / Bulanan dengan persentase pertumbuhan (+18.4%).
  - Kartu Estimasi Laba Bersih (khusus akun Owner).
  - Kartu Pesanan Perlu Diproses & Dikirim (Admin).
  - Kartu Antrean Inspeksi Unit Tukar Tambah.
- Visualisasi Grafik: Area chart transparan dengan gradien halus menampilkan omzet 30 hari terakhir.
- Tabel Pesanan Interaktif: Badges status berwarna, tombol cetak resi cepat, tombol kirim update WhatsApp manual (jika diperlukan).
