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
- **Panel Admin & Owner Hallmark Luxury / Editorial Redesign (Fase 17–24)**: seluruh halaman operasional dan analitik eksekutif (dashboard admin, produk, pesanan, trade-in, dashboard owner, laporan keuangan, staf, konfigurasi) diredesain menggunakan standar keahlian **Hallmark Redesign** dengan tone **Luxury / Editorial** (kombinasi display serif anggun, grotesque tajam, buku besar *Trade Ledger*, palet limewash hangat, aksen titanium slate, hairlines presisi, dan kepatuhan penuh aturan *The Floor* — lihat §4.5).
- **Etalase Belanja Customer Hallmark Luxury Retail & Studio Redesign (Fase 25–32)**: seluruh halaman etalase publik (layout master, beranda showcase flagship, katalog filter multi-kondisi, halaman detail produk / PDP Split Studio, keranjang belanja, checkout Midtrans Snap, wizard trade-in 4 langkah, riwayat pesanan, konfirmasi pembayaran) diredesain menggunakan standar keahlian **Hallmark Redesign** dengan tone **Luxury Retail & Studio Precision** (kombinasi kanvas limewash alabaster, kartu cotton stock, display serif Sentient berbobot ringan, grotesque Hanken Grotesk / Geist dengan `tabular-nums`, aksen titanium slate & retail blue, tata letak anatomi *job: buy*, rasio 80 kata per layar, dan kepatuhan penuh aturan *The Floor* — lihat §4.6).

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

#### 4.5. Standar UI/UX Panel Admin & Owner — Hallmark Luxury / Editorial Redesign (`Fase 17–24`)
Panel Admin Operasional dan Pemilik Usaha (Owner) mengadopsi kerangka kerja **Hallmark Redesign** dengan arahan estetika **Luxury / Editorial** (mengacu pada `blueprint.md` §6.3 dan keahlian desain `.agent/skills/hallmark/`):

1. **Filosofi & Karakter Visual Luxury Editorial**:
   - Memadukan presisi tinggi retail jam tangan & gawai mewah dengan ketenangan majalah editorial independen (*high-end atelier & trade ledger aesthetic*).
   - Menghilangkan kesan dasbor SaaS generik berbasis template AI (*AI Slop*): tanpa drop shadow tebal berwarna-warni, tanpa tombol gradien ungu-ke-biru, dan tanpa kartu bersarang (*card-in-card*).
2. **Materialitas, Palet Warna & Permukaan**:
   - Kanvas Limewash Alabaster (`#FBFBFA` / `oklch(96% 0.008 80)`), permukaan kartu *Cotton Card Stock* (`#FFFFFF` dengan batas luar *hairline* `1px solid #E5E2DC`), tinta utama obsidian pekat (`#141413` / `oklch(19% 0.024 52)`), teks sekunder *pencilled slate* (`#6B6864`), dan aksen tunggal *Cool Slate Titanium* (`#2C353D` / `oklch(41% 0.075 248)`).
3. **Tipografi Editorial Bersahaja**:
   - **Display & Heading Eksekutif**: Menggunakan font serif anggun utama **Sentient** (Fontshare CDN, bobot *Light 300 / Regular 400*, fallback: *Playfair Display* / *Georgia*), line-height rapat, sentence case, memberikan wibawa tenang pada judul modul dan nominal pernyataan omzet/laba.
   - **Teks Tubuh & Data Moneter**: Menggunakan font grotesque utama **Hanken Grotesk** (Google Fonts CDN, bobot *400 / 500*, fallback: *Inter* / *system-ui*) dengan `tabular-nums` untuk perataan angka finansial, tanggal, nomor IMEI, dan stok.
   - **Label & Navigasi**: Huruf berukuran setingkat lebih kecil, *untracked*, tanpa huruf kapital kecil (*small caps*) atau huruf kapital renggang (*tracked capitals*).
4. **Format Buku Kas / Master Trade Ledger**:
   - Seluruh tabel operasional (pesanan, produk, verifikasi trade-in, laporan laba rugi) disusun dengan format buku besar: garis ganda (*double rule*) pada kepala tabel, garis tipis (*hairlines 1px*) antar baris, angka moneter rata kanan, serta status transaksi menggunakan badge stempel resmi (*editorial stamp badge*).
5. **Kepatuhan Terhadap Hallmark "The Floor"**:
   - Seluruh warna dan font wajib bersumber dari token CSS deklarasi `:root` (tanpa warna literal di dalam view).
   - Dilarang keras memakai `transition: all` dan dilarang menganimasikan `width`, `height`, `top`, `left`.
   - Wajib memiliki `:focus-visible` instan pada setiap elemen interaktif dan fallback `prefers-reduced-motion: reduce`.
   - Menggunakan `overflow-x: clip` pada kontainer root (bukan `hidden` atau `100vw`).
   - Tidak memuat metrik fiktif atau elemen dekoratif palsu; data yang ditampilkan mencerminkan kondisi riil database.
6. **Preservasi Konten & Wiring Backend**:
   - Seluruh rute Laravel, aksi Controller, form handler, CSRF token, validasi, dan alur logic status pesanan/trade-in dipertahankan utuh tanpa perubahan fungsional (*zero breaking change*).
7. **Cakupan Halaman**:
   - Fase 17 `/admin/dashboard`, Fase 18 `/admin/products`, Fase 19 `/admin/orders`, Fase 20 `/admin/trade-ins`, Fase 21 `/owner/dashboard`, Fase 22 `/owner/financial-report`, Fase 23 `/owner/staff`, Fase 24 `/admin/settings`.

---

#### 4.6. Standar UI/UX Customer Storefront — Hallmark Luxury Retail Redesign (`Fase 25–32`)
Etalase belanja pelanggan (*Customer Storefront*) ditransformasi menggunakan kerangka kerja **Hallmark Redesign** dengan estetika **Luxury Retail & Studio Precision** (genre: `editorial` / `modern-minimal` hybrid, mengacu pada `blueprint.md` §6.5 dan keahlian desain `.agent/skills/hallmark/`):

1. **Filosofi Visual & Anti-AI Slop**:
   - Menggantikan template e-commerce generik (kartu bersarang/card-in-card, drop shadow tebal berwarna-warni, gradien ungu-ke-biru, dan animasi lambat tak menentu) dengan ketenangan etalase studio perangkat keras fisik Apple yang presisi.
   - Mengadopsi prinsip *"One Idea Per Screen"* dan batasan ~80 kata per layar pandang (F13): informasi harga, spesifikasi teknis, kondisi unit, dan jaminan garansi ditampilkan secara jujur dan padat tanpa teks basa-basi (*marketing fluff*).
2. **Anatomi Pekerjaan Pengunjung (*job: buy*) & Pilihan Makrostruktur**:
   - Berdasarkan `references/anatomy.md` untuk pekerjaan pembeli (`buy`): penawaran utama (*Offer*) menjadi fokus hero; rentang varian (*range*) disajikan dalam baris/grid dengan harga seketika; produk ditampilkan dalam jarak dekat pencahayaan studio nyata (*true color daylight photography*); rincian pengiriman kurir dan jaminan garansi disajikan sebagai fakta terukur; aksi pembelian menempel di layar (*Sticky Purchase Bar*); tanpa form newsletter palsu di penutup halaman.
   - **Makrostruktur Terpilih (`macrostructures.md`)**:
     - Beranda: **Bento Grid (01)** & **Catalogue (11)** untuk kurasi seri iPhone dan pilar layanan toko.
     - Katalog: **Catalogue (11)** sebagai indeks visual inventaris jujur ber-grid rapi.
     - Halaman Detail Produk (PDP): **Split Studio (15)** dengan galeri studio vertikal menempel (*sticky*) di kolom kiri dan konfigurator langkah-demi-langkah di kolom kanan.
     - Wizard Trade-In: **Narrative Workflow (14)** alur penaksiran unit 4-tahap yang transparan.
3. **Materialitas, Palet Warna & Permukaan**:
   - Kanvas Limewash Studio (`--cust-paper: oklch(97.2% 0.006 215)` / `#FBFBFA`), permukaan kartu *Cotton Stock* (`--cust-surface: #FFFFFF` dengan hairline border `1px solid oklch(88% 0.008 75)` / `#E5E2DC`), tinta teks obsidian pekat (`--cust-ink: oklch(16% 0.032 250)`), teks sekunder *pencilled slate* (`--cust-ink-muted`), aksen titanium slate (`--cust-accent: oklch(41% 0.075 248)`), dan sinyal biru retail Apple (`--cust-signal: #0071E3`).
   - Lapisan kaca susu daylight (`--cust-glass: rgba(255, 255, 255, 0.88)` dengan `backdrop-filter: blur(20px)`) khusus pada Sticky Header dan Sticky Bottom Purchase Bar.
4. **Tipografi Presisi & Tabular**:
   - **Judul Display & Nama Seri**: Font serif berwibawa ringan **Sentient** (Fontshare CDN, bobot 300/400) untuk tajuk utama hero dan nama seri iPhone.
   - **Teks Spesifikasi & Nilai Moneter**: Font grotesque tajam **Hanken Grotesk** / **Geist** dengan format `tabular-nums` untuk perataan angka rupiah, persentase *Battery Health*, nomor IMEI, dan kapasitas memori.
   - **Label & Navigasi**: Huruf berukuran setingkat lebih kecil, *untracked*, tanpa huruf kapital kecil (*small caps*) atau huruf kapital renggang (*tracked capitals*).
5. **Kepatuhan Terhadap Hallmark "The Floor" (F1–F16)**:
   - Seluruh warna dan font wajib bersumber dari token CSS deklarasi `:root` di `resources/css/customer.css` (tanpa warna literal).
   - Dilarang keras memakai `transition: all`; dilarang menganimasikan `width`, `height`, `top`, `left`.
   - Menggunakan `overflow-x: clip` pada kontainer root (bukan `hidden` atau `100vw`).
   - Setiap elemen interaktif wajib memiliki `:focus-visible` seketika dan fallback `prefers-reduced-motion: reduce`.
   - Tidak memuat metrik ulasan fiktif, kutipan pelanggan palsu, atau logo wall buatan; hanya menampilkan data nyata dari database MySQL.
6. **Preservasi Logika Backend & Integrasi (Zero Breaking Change)**:
   - Rute belanja publik, keranjang (`CartController`), checkout (`CheckoutController`), integrasi Midtrans Snap popup, kalkulator Trade-In (`TradeInController`), notifikasi WhatsApp Fonnte & Email SMTP, generator Nota PDF DomPDF, serta autentikasi mandiri pelanggan (Fase 16) tetap utuh 100%.
7. **Cakupan Fase Kerja (Fase 25–32)**:
   - Fase 25: Foundation, Token CSS & Master Layout (`layouts/customer.blade.php`)
   - Fase 26: Beranda & Showcase Flagship (`customer/home.blade.php`)
   - Fase 27: Katalog & Filter Multi-Kondisi (`customer/catalog.blade.php`)
   - Fase 28: Detail Produk / PDP Split Studio (`customer/pdp.blade.php`)
   - Fase 29: Keranjang & Checkout Midtrans (`customer/cart.blade.php`, `customer/checkout.blade.php`)
   - Fase 30: Wizard Trade-In iPhone (`customer/trade-in.blade.php`, `trade-in-detail.blade.php`)
   - Fase 31: Riwayat Pesanan & Nota PDF (`customer/orders.blade.php`, `checkout-success.blade.php`)
   - Fase 32: Audit Akhir Sloplint, The Floor Sweep & QA E2E Testing

---

#### 4.7. Alur Kerja Terpadu & Interaksi Antar-Peran (End-to-End Multi-Role Workflow)
Sistem Rap-iPhone menghubungkan 3 peran pengguna (**Customer**, **Admin Operasional**, dan **Owner**) dalam siklus terpadu yang menjamin keandalan data dari etalase toko hingga pembukuan keuangan:

1. **Alur Transaksi & Pemenuhan Pesanan (Order Fulfillment Lifecycle)**:
   - **Customer**: Memilih varian unit iPhone (Warna, Kapasitas, BH, IMEI) $\rightarrow$ Masuk Keranjang $\rightarrow$ Mengisi Alamat & Kurir (JNE / SiCepat / J&T / Pick-up Store) $\rightarrow$ Membayar tagihan via Midtrans Snap.
   - **Sistem Otomatis (Midtrans Webhook)**: Memverifikasi tanda tangan SHA512 $\rightarrow$ Mengubah status pesanan menjadi `paid` $\rightarrow$ Memotong kuantitas stok varian secara atomik dengan *row-level locking* (`lockForUpdate`) guna mencegah *overselling* $\rightarrow$ Mengirim notifikasi WhatsApp dan Email konfirmasi pembayaran beserta lampiran Nota PDF resmi ber-QR Code.
   - **Admin Operasional**: Memantau antrean pesanan berstatus `paid` $\rightarrow$ Menyiapkan unit fisik dan mengalokasikan nomor IMEI unit pada pesanan $\rightarrow$ Mengemas barang dan mencetak Label Pengiriman massal $\rightarrow$ Menginput nomor resi pengiriman kurir (`shipping_tracking_number`) dan mengubah status menjadi `shipped` (yang otomatis memicu notifikasi WhatsApp & Email resi ke customer).
   - **Customer**: Menerima paket fisik $\rightarrow$ Memeriksa unit fisik dan memvalidasi keaslian nota via QR Code `/verifikasi-nota/{orderNumber}/{code}`.
   - **Owner**: Transaksi lunas secara *real-time* tercatat pada *Executive Dashboard* dan laporan laba rugi: sistem menghitung Omzet Penjualan, HPP unit terjual (*Cost of Goods Sold*), dan Laba Bersih (*Net Profit*).

2. **Alur Program Tukar Tambah / Buyback (Trade-In Lifecycle)**:
   - **Customer**: Melakukan kalkulasi estimasi nilai iPhone lama via 4-Step Wizard $\rightarrow$ Memilih opsi pencairan: **Transfer Rekening Bank** atau **Kupon Voucher Belanja Toko** $\rightarrow$ Mengajukan permohonan (`submitted`) $\rightarrow$ Mengirimkan atau membawa unit lama ke gerai Rap-iPhone.
   - **Admin / Teknisi**: Menerima unit di toko dan memperbarui status ke `unit_received` $\rightarrow$ Menjalankan inspeksi 4 dimensi (kondisi fisik bodi, layar/TrueTone/FaceID, kesehatan baterai, fungsi kamera & IMEI) $\rightarrow$ Memperbarui hasil inspeksi (`inspected`) dan menentukan harga penawaran final (`final_price`) $\rightarrow$ Memutuskan persetujuan (`approved` atau `rejected`).
   - **Sistem**: Mengirimkan notifikasi penawaran harga final dan status persetujuan ke WhatsApp & Email pelanggan secara seketika.
   - **Pencairan & Pemanfaatan**:
     - *Jika Transfer Bank*: Admin memproses pembayaran tunai dan menandai status `paid_out`.
     - *Jika Kupon Toko*: Kode voucher potongan belanja diaktifkan dan dapat digunakan customer langsung sebagai diskon pada saat checkout unit iPhone baru.
   - **Owner**: Nilai unit yang diakuisisi dicatat sebagai arus kas keluar (*Cash Outflow Trade-In*) pada laporan keuangan eksekutif, dan unit fisik dapat dimasukkan kembali oleh Admin ke inventaris iPhone *Second Teruji*.

3. **Alur Manajemen Inventaris & Kontrol Produk**:
   - **Admin**: Menginput master seri iPhone, varian kapasitas, warna resmi, persentase Battery Health, nomor IMEI unit, harga modal (HPP), dan harga jual retail $\rightarrow$ Mengunggah galeri multi-angle resolusi tinggi $\rightarrow$ Memantau peringatan stok menipis (*low stock alert* $\le$ 3).
   - **Customer**: Menikmati transparansi penuh pada etalase toko (status garansi TAM/iBox vs Second QC 32-titik, Battery Health riil, foto asli tanpa AI fluff).
   - **Owner**: Memperoleh kepastian perhitungan margin laba kotor (*Gross Margin*) berbasis HPP yang akurat pada setiap unit yang terjual.

4. **Alur Tata Kelola Eksekutif & Staf (Owner Governance)**:
   - **Owner**: Memonitor KPI bisnis melalui Executive Dashboard (Omzet, HPP, Laba Bersih, Tren Penjualan 30 Hari, Produk Terlaris, dan Rasio Konversi).
   - **Owner**: Mengaudit dan mengekspor buku besar Laba Rugi dan Arus Kas ke format CSV/Excel (dengan UTF-8 BOM) dan PDF Eksekutif.
   - **Owner**: Memiliki wewenang eksklusif mengelola akun staf toko (`role: admin`), mereset kredensial, atau menonaktifkan akses staf yang telah berhenti.

---

#### 4.8. Invarian Logika Bisnis & Aturan Integritas Data (Business Invariants)
Untuk menjaga keandalan sistem berskala produksi, platform Rap-iPhone menerapkan aturan logika bisnis yang tidak boleh dilanggar (*non-waivable business invariants*):
1. **Integritas Stok & Pencegahan Overselling**: Pemotongan kuantitas stok varian hanya terjadi sekali saat webhook pembayaran Midtrans berstatus `settlement/capture` (lunas) dengan menggunakan transaksi database atomik dan *pessimistic row locking* (`lockForUpdate`). Kuantitas stok tidak boleh bernilai negatif (`max(0, stock - qty)`).
2. **Idempotensi Webhook Callback**: Webhook Midtrans yang diterima berulang kali untuk pesanan yang sudah berstatus `paid` tidak boleh mengurangi stok lagi atau memicu duplikasi notifikasi.
3. **Preservasi Snapshot HPP Historis**: Saat transaksi dibuat, nilai `cost_price` (HPP) varian disalin secara permanen ke tabel `order_items`. Jika Admin mengubah master HPP produk di masa depan, laporan laba rugi historis transaksi masa lalu tetap akurat 100%.
4. **Isolasi Jalur Autentikasi (Strict Role-Isolated Auth)**: Portal `/login` khusus pelanggan, `/admin/login` khusus staf operasional, dan `/owner/login` khusus pemilik usaha. Upaya login silang (*cross-portal login*) wajib ditolak dengan pesan kesalahan yang jelas dan pengalihan ke portal yang sah.
5. **Integritas Alokasi IMEI**: Setiap unit fisik yang dikirimkan kepada pelanggan wajib dialokasikan nomor IMEI unik pada detail pesanan (`allocated_imei`) sebelum status diubah menjadi `shipped`. Nomor IMEI ini dicantumkan pada Faktur PDF dan menjadi rujukan klaim garansi toko.
6. **Perlakuan Finansial Kupon vs Transfer Bank**: Pencairan Trade-In berbasis transfer bank dicatat sebagai pengeluaran tunai langsung (*Cash Outflow*), sedangkan pencairan berbasis voucher toko dicatat sebagai kewajiban potongan belanja (*Discount Allowance*) yang mengurangi penerimaan kotor pesanan target.

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
   - **Panel Admin & Owner (Fase 17–24)**: Menerapkan standar **Hallmark Luxury / Editorial Redesign** sesuai §4.5 dengan kepatuhan terhadap aturan *The Floor* dan lulus audit linter Hallmark (`node .agent/skills/hallmark/scripts/sloplint.mjs resources/views/admin resources/views/owner --genre editorial`).
   - **Etalase Pelanggan / Customer Storefront (Fase 25–32)**: Menerapkan standar **Hallmark Luxury Retail & Studio Redesign** sesuai §4.6 dengan kepatuhan terhadap 16 aturan non-waivable *The Floor* dan lulus audit linter Hallmark (`node .agent/skills/hallmark/scripts/sloplint.mjs resources/views/customer resources/views/layouts/customer.blade.php --genre editorial`). Dilarang memakai kelas tema gelap, gradien ungu-ke-biru, atau warna literal di luar token CSS deklarasi `:root`.
3. **Kemudahan Pemeliharaan (Maintainability)**:
   - Arsitektur kode bersih berorientasi layanan (*Service Layer Pattern*) untuk Midtrans, Fonnte, Mailer, dan PDF Generator.
   - Konfigurasi kredensial terisolasi penuh pada file `.env`.

---

### 6. Matriks Status Pesanan (Order State Machine)
- `pending`: Menunggu pembayaran via Midtrans Snap.
- `paid`: Pembayaran terverifikasi lunas (Webhook); stok unit berkurang secara otomatis dan aman (*row lock*).
- `processing`: Pesanan sedang dipersiapkan dan nomor seri/IMEI unit dialokasikan oleh Admin.
- `shipped`: Pesanan telah diserahkan ke ekspedisi kurir, nomor resi terbit, notifikasi WhatsApp/Email terkirim otomatis ke customer.
- `completed`: Paket diterima oleh customer, masa garansi unit aktif.
- `cancelled`: Pesanan dibatalkan (kadaluarsa Midtrans / pembatalan sistem); stok dikembalikan jika sebelumnya terpotong.

---

### 7. Matriks Status Tukar Tambah (Trade-In State Machine)
- `submitted`: Customer telah mengirimkan form taksiran unit lama; menunggu unit diserahkan/dikirimkan ke toko.
- `unit_received`: Unit fisik telah tiba di gerai Rap-iPhone dan diterima oleh staf toko.
- `inspected`: Teknisi telah selesai melakukan inspeksi fisik 4-titik (body, layar, baterai, IMEI/fungsi) dan memperbarui nilai penawaran final.
- `approved`: Penawaran disetujui; jika memilih kupon belanja maka kode voucher aktif, jika memilih transfer bank maka antre pencairan.
- `rejected`: Pengajuan ditolak (misal unit bypass, iCloud terkunci, atau tidak memenuhi syarat minimum toko); unit dikembalikan ke customer.
- `paid_out`: Dana tunai telah ditransfer ke rekening bank customer atau voucher belanja telah berhasil digunakan pada pesanan unit baru.

---

### 8. Roadmap Lanjutan: Audit, Review & Perbaikan Logika Bisnis (Fase 33)
- Menjamin keterpaduan 100% tanpa celah antara aksi antarmuka Customer, pemenuhan operasional Admin, dan pembukuan eksekutif Owner.
- Verifikasi otomatis melalui unit/feature test suite (`tests/Feature/EndToEndWorkflowTest.php`) yang mencakup seluruh siklus pesanan, tukar tambah, mitigasi race condition stok, dan rekonsiliasi keuangan.
