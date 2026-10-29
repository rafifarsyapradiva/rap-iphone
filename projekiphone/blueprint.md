# ARCHITECTURE & SYSTEM BLUEPRINT
## Platform E-Commerce Jual Beli iPhone (Rap-iPhone)

---

### 1. Diagram Arsitektur Sistem

```
+-----------------------------------------------------------------------------------+
|                    CLIENT LAYER (HALLMARK LUXURY RETAIL & EDITORIAL SUITE)        |
|  +---------------------------+  +-----------------------------------------------+  |
|  | Customer Storefront       |  | Admin & Owner Executive Console               |  |
|  | (Hallmark Luxury Retail & |  | (Hallmark Luxury / Editorial Redesign Suite:  |  |
|  |  Studio Precision:       |  |  Sentient Serif, Trade Ledgers, Cotton Stock) |  |
|  |  Sentient, Split Studio)  |  |                                               |  |
|  +---------------------------+  +-----------------------------------------------+  |
+----------------------------------------+------------------------------------------+
                                         | HTTP / HTTPS Requests
                                         v
+-----------------------------------------------------------------------------------+
|                           LARAVEL 12 APPLICATION CORE                             |
|                                                                                   |
|  [ ROUTING & MIDDLEWARE ]                                                         |
|  + web.php, api.php                                                               |
|  + Multi-Portal Auth Routes:                                                      |
|    - Fase 14 Admin   : /admin/login, /admin/logout                                |
|    - Fase 15 Owner   : /owner/login, /owner/logout                                |
|    - Fase 16 Customer: /login, /register, /auth/google, /logout                   |
|  + AuthMiddleware, RoleMiddleware (Contextual Guest Redirect: customer|admin|owner)|
|  + View Composer layouts.admin -> $shell (badge antrean & status integrasi)       |
|  + VerifyCsrfToken (kecuali webhook endpoint api/midtrans/callback)               |
|                                                                                   |
|  [ CONTROLLER LAYER ]                                                             |
|  + Auth\AdminAuthController (Fase 14 — Jalur Login Admin Sendiri)                 |
|  + Auth\OwnerAuthController (Fase 15 — Jalur Login Owner Sendiri)                 |
|  + Auth\AuthController & GoogleAuthController (Fase 16 — Login/Register Customer) |
|  + ProductController & CatalogController                                          |
|  + CartController & CheckoutController                                            |
|  + TradeInController                                                              |
|  + Admin\OrderController, Admin\ProductController, Admin\TradeInController        |
|  + Owner\DashboardController, Owner\FinancialReportController                     |
|                                                                                   |
|  [ SERVICE LAYER ]                                                                |
|  + App\Services\MidtransService      -> Snap Token Creation & Status Sync         |
|  + App\Services\FonnteService        -> WhatsApp Notifications API Client         |
|  + App\Services\InvoicePdfService    -> DomPDF Generator Engine                   |
|  + App\Services\NotificationService  -> Multi-channel Orchestrator (WA + Email)   |
|                                                                                   |
|  [ EVENT & LISTENER DISPATCH ]                                                    |
|  + OrderCreatedEvent                 -> Notif WA & Email Tagihan                  |
|  + PaymentSuccessfulEvent            -> Notif WA Lunas + Email + Lampiran Nota PDF|
|  + OrderShippedEvent                 -> Notif WA Resi Pengiriman                  |
+--------------------+---------------------------------------+----------------------+
                     |                                       |
                     v                                       v
+------------------------------------+   +------------------------------------------+
|          DATABASE LAYER            |   |          EXTERNAL INTEGRATIONS           |
|  MySQL (phpMyAdmin)                |   |  + Google Console OAuth 2.0 API          |
|  + users, roles                    |   |  + Midtrans Payment Gateway (Snap API)   |
|  + categories, products, variants  |   |  + Midtrans Webhook (Callback HTTP POST) |
|  + orders, order_items, payments   |   |  + Fonnte WhatsApp Gateway API           |
|  + trade_ins, notification_logs    |   |  + Mail SMTP (Gmail / Mailgun / Brevo)   |
+------------------------------------+   +------------------------------------------+
```

---

### 2. Skema Basis Data (Database Schema & Relasi)

#### 2.1. Tabel `users`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT (PK, Auto Inc) | Primary Key |
| `name` | VARCHAR(255) | Nama lengkap pengguna |
| `email` | VARCHAR(255) (Unique) | Alamat email terverifikasi |
| `password` | VARCHAR(255) (Nullable) | Password hash (nullable jika via Google Login) |
| `google_id` | VARCHAR(255) (Nullable) | Identitas akun Google OAuth |
| `phone_number` | VARCHAR(20) (Nullable) | Nomor WhatsApp (format: 08xx / 628xx) |
| `role` | ENUM('customer','admin','owner') | Default: 'customer' |
| `avatar` | VARCHAR(255) (Nullable) | URL atau path foto profil |
| `address` | TEXT (Nullable) | Alamat utama customer |
| `city` | VARCHAR(100) (Nullable) | Kota/Kabupaten pengiriman |
| `postal_code` | VARCHAR(10) (Nullable) | Kode pos |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & modifikasi |

#### 2.2. Tabel `categories` & `products`
**`categories`**:
- `id` (PK), `name` (VARCHAR 100), `slug` (VARCHAR 120), `icon` (VARCHAR 100).

**`products`**:
- `id` (PK, BIGINT)
- `category_id` (FK -> `categories.id`)
- `name` (VARCHAR 255) -> Contoh: *"iPhone 16 Pro Max"*
- `slug` (VARCHAR 255, Unique)
- `series` (VARCHAR 50) -> Contoh: *"iPhone 16 Series"*
- `condition` (ENUM: `'new'`, `'second'`) -> Baru atau Bekas
- `grade` (VARCHAR(10), Nullable) -> `'Grade A+'`, `'Grade A'`, `'Grade B'` (khusus unit second)
- `warranty_type` (ENUM: `'resmi_ibox'`, `'resmi_digimap'`, `'inter_all_operator'`, `'toko'`)
- `base_price` (DECIMAL 12,2) -> Harga awal terendah untuk display
- `description` (LONGTEXT) -> Spesifikasi detail & ketentuan garansi
- `is_active` (BOOLEAN) -> Default `true`
- `created_at`, `updated_at` (TIMESTAMP)

#### 2.3. Tabel `product_variants` & `product_images`
**`product_variants`**:
- `id` (PK, BIGINT)
- `product_id` (FK -> `products.id`, Cascade Delete)
- `storage_capacity` (VARCHAR 20) -> Contoh: *"128GB"*, *"256GB"*, *"512GB"*, *"1TB"*
- `color_name` (VARCHAR 50) -> Contoh: *"Natural Titanium"*, *"Desert Titanium"*
- `color_hex` (VARCHAR 10) -> Kode warna heksadesimal untuk dot color picker
- `cost_price` (DECIMAL 12,2) -> Harga Pokok Penjualan (HPP untuk kalkulasi laba Owner)
- `selling_price` (DECIMAL 12,2) -> Harga jual retail ke customer
- `stock` (INT) -> Sisa kuantitas unit
- `sku` (VARCHAR 100, Unique) -> Kode stok unik
- `battery_health` (INT, Nullable) -> Nilai % kesehatan baterai (khusus second, misal 95%)
- `imei` (VARCHAR 30, Nullable) -> Nomor IMEI fisik (untuk transparansi unit)
- `created_at`, `updated_at` (TIMESTAMP)

**`product_images`**:
- `id` (PK, BIGINT), `product_id` (FK), `variant_id` (FK Nullable), `image_path` (VARCHAR 255), `is_primary` (BOOLEAN), `order_index` (INT).

#### 2.4. Tabel `orders`, `order_items` & `payments`
**`orders`**:
- `id` (PK, BIGINT)
- `order_number` (VARCHAR 50, Unique) -> Contoh: `RAP-20261028-001`
- `user_id` (FK -> `users.id`)
- `total_items_price` (DECIMAL 12,2)
- `shipping_cost` (DECIMAL 12,2)
- `discount_amount` (DECIMAL 12,2, Default 0)
- `grand_total` (DECIMAL 12,2)
- `status` (ENUM: `'pending'`, `'paid'`, `'processing'`, `'shipped'`, `'completed'`, `'cancelled'`)
- `shipping_courier` (VARCHAR 50) -> JNE, SiCepat, J&T, atau Pick-up Toko
- `shipping_tracking_number` (VARCHAR 100, Nullable) -> Nomor resi pengiriman
- `shipping_address` (TEXT)
- `recipient_name` (VARCHAR(150))
- `recipient_phone` (VARCHAR(30))
- `customer_notes` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

**`order_items`**:
- `id` (PK, BIGINT)
- `order_id` (FK -> `orders.id`, Cascade Delete)
- `product_variant_id` (FK -> `product_variants.id`)
- `product_name` (VARCHAR 255)
- `variant_details` (VARCHAR 255) -> Simpan snapshot: *"Desert Titanium / 256GB"*
- `price` (DECIMAL 12,2)
- `cost_price` (DECIMAL 12,2) -> Snapshot HPP saat transaksi
- `quantity` (INT)
- `subtotal` (DECIMAL 12,2)
- `allocated_imei` (VARCHAR 50, Nullable)

**`payments`**:
- `id` (PK, BIGINT)
- `order_id` (FK -> `orders.id`)
- `midtrans_transaction_id` (VARCHAR 100, Nullable)
- `payment_type` (VARCHAR 50, Nullable) -> `qris`, `bank_transfer`, `gopay`, `credit_card`
- `gross_amount` (DECIMAL 12,2)
- `snap_token` (VARCHAR 255, Nullable)
- `transaction_status` (VARCHAR 50) -> `pending`, `settlement`, `expire`, `deny`, `cancel`
- `payment_time` (TIMESTAMP, Nullable)
- `raw_payload` (JSON, Nullable) -> Payload lengkap dari Midtrans Webhook
- `created_at`, `updated_at` (TIMESTAMP)

#### 2.5. Tabel `trade_ins` (Program Jual / Tukar Tambah iPhone)
- `id` (PK, BIGINT)
- `trade_in_code` (VARCHAR 50, Unique) -> Contoh: `TI-20261028-001`
- `user_id` (FK -> `users.id`)
- `target_order_id` (FK -> `orders.id`, Nullable) -> Terhubung jika dijadikan potongan unit baru
- `phone_model` (VARCHAR 100) -> Contoh: *"iPhone 13 Pro"*
- `storage_capacity` (VARCHAR 20) -> *"128GB"*
- `battery_health` (INT) -> Contoh: 87
- `physical_condition` (VARCHAR 50) -> `'Mulus (Grade A)'`, `'Lecet Pemakaian (Grade B)'`
- `screen_condition` (VARCHAR 50) -> `'Normal'`, `'Ada Jamur/TrueTone Off'`, `'Retak'`
- `accessories_status` (VARCHAR 50) -> `'Fullset Box Original'`, `'Unit Only'`
- `imei` (VARCHAR 30, Nullable)
- `estimated_price` (DECIMAL 12,2) -> Harga taksiran otomatis sistem
- `final_price` (DECIMAL 12,2, Nullable) -> Harga deal setelah inspeksi admin di toko
- `status` (ENUM: `'submitted'`, `'unit_received'`, `'inspected'`, `'approved'`, `'rejected'`, `'paid_out'`)
- `customer_bank_name` (VARCHAR 50, Nullable)
- `customer_account_number` (VARCHAR 50, Nullable)
- `customer_account_holder` (VARCHAR 100, Nullable)
- `admin_notes` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

#### 2.6. Tabel `notifications_log`
- `id` (PK, BIGINT)
- `channel` (ENUM: `'whatsapp'`, `'email'`)
- `recipient` (VARCHAR 150) -> Nomor HP / Email
- `event_name` (VARCHAR 100) -> `order_created`, `payment_success`, `order_shipped`
- `status` (ENUM: `'sent'`, `'failed'`)
- `error_message` (TEXT, Nullable)
- `response_payload` (JSON, Nullable)
- `created_at` (TIMESTAMP)

---

### 3. Arsitektur Integrasi Layanan (Service Layer)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── AdminAuthController.php   <-- Fase 14: Jalur Login Admin Sendiri (/admin/login)
│   │   │   ├── OwnerAuthController.php   <-- Fase 15: Jalur Login Owner Sendiri (/owner/login)
│   │   │   ├── AuthController.php        <-- Fase 16: Jalur Login & Register Customer (/login, /register)
│   │   │   └── GoogleAuthController.php  <-- Fase 16: Google OAuth Khusus Customer (Socialite)
│   │   ├── Customer/
│   │   │   ├── HomeController.php
│   │   │   ├── CatalogController.php
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── OrderHistoryController.php
│   │   │   └── TradeInCustomerController.php
│   │   ├── Admin/
│   │   │   ├── ProductManagementController.php
│   │   │   ├── OrderManagementController.php
│   │   │   └── TradeInVerificationController.php
│   │   ├── Owner/
│   │   │   ├── ExecutiveDashboardController.php
│   │   │   ├── FinancialReportController.php
│   │   │   └── StaffManagementController.php
│   │   └── Api/
│   │       └── MidtransCallbackController.php  <-- Webhook Handler
│   └── Middleware/
│       └── RoleMiddleware.php                  <-- RBAC Guard & Contextual Portal Redirect
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── ProductVariant.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Payment.php
│   └── TradeIn.php
├── Services/
│   ├── MidtransService.php         <-- Generate Snap Token & Cek Status
│   ├── FonnteService.php           <-- Kirim Pesan WhatsApp via Fonnte API
│   ├── InvoicePdfService.php       <-- Render Nota PDF DomPDF
│   └── NotificationOrchestrator.php<-- Trigger WA + Email serentak
└── Mail/
    ├── OrderInvoiceMail.php        <-- Mailable dengan attachment Nota PDF
    └── PaymentConfirmedMail.php
```

---

### 4. Spesifikasi Integrasi API Pihak Ketiga

#### 4.1. Google Console OAuth 2.0 (Laravel Socialite — Khusus Customer Fase 16)
- **Library**: `laravel/socialite`
- **Redirect URI**: `https://domain.com/auth/google/callback`
- **Logika Autentikasi Pelanggan**:
  1. Pelanggan klik "Masuk dengan Google" atau "Daftar 1-Klik dengan Google" pada halaman `/login` atau `/register`.
  2. Redirect ke Google Consent Screen.
  3. Callback menerima token dan profil: `email`, `name`, `id`, `avatar`.
  4. Cari user berdasarkan `google_id` atau `email`.
  5. **Isolasi Role (`Fase 16`)**: Jika akun yang ditemukan memiliki role `admin` atau `owner`, proses login Google ditolak dan diarahkan untuk masuk melalui portal internal (`/admin/login` atau `/owner/login`).
  6. Jika user `customer` sudah ada, sinkronkan `google_id` dan *login*. Jika belum, buat record baru dengan role `customer`.

#### 4.2. Midtrans Payment Gateway (Snap API & Callback)
- **Library**: `midtrans/midtrans-php`
- **Konfigurasi `.env`**:
  - `MIDTRANS_SERVER_KEY=...`
  - `MIDTRANS_CLIENT_KEY=...`
  - `MIDTRANS_IS_PRODUCTION=false`
  - `MIDTRANS_IS_SANITIZED=true`
  - `MIDTRANS_IS_3DS=true`
- **Pembuatan Token Snap**:
  - Dikirim saat customer menekan "Lanjut ke Pembayaran" di halaman checkout.
  - Memuat rincian `transaction_details`, `item_details`, dan `customer_details`.
  - Frontend memanggil fungsi `snap.pay(snapToken)`.
- **Penanganan Webhook (`/api/midtrans/callback`)**:
  - Mengabaikan proteksi CSRF di middleware.
  - **Validasi Keamanan**: Membuat hash verifikasi:
    `hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey)`
    Status hanya diupdate bila signature hash cocok dengan kiriman header Midtrans.
  - Update status pesanan:
    - `settlement` / `capture (accept)` -> Status `paid`.
    - `pending` -> Tetap `pending`.
    - `deny` / `expire` / `cancel` -> Status `cancelled`.
  - Trigger *NotificationOrchestrator* untuk mengirim WhatsApp & Email bukti pembayaran.

#### 4.3. Notifikasi WhatsApp (Fonnte API)
- **Endpoint**: `https://api.fonnte.com/send`
- **Header Autentikasi**: `Authorization: {FONNTE_API_TOKEN}`
- **Fungsi Layanan**:
  - Format nomor telepon otomatis disesuaikan ke format internasional (`628xxx`).
  - Template pesan dinamis:
    - Tagihan pesanan baru & detail rekening / tautan pembayaran.
    - Konfirmasi pembayaran sukses beserta ringkasan unit iPhone & nomor nota.
    - Pembaruan nomor resi logistik saat paket dikirim.
    - Notifikasi taksiran & persetujuan unit tukar tambah.

#### 4.4. Generator Nota PDF (DomPDF)
- **Library**: `barryvdh/laravel-dompdf`
- **Desain Template**: Desain faktur resmi bergaya Apple Authorized Partner:
  - Header: Logo Rap-iPhone, Alamat Store, Kontak Resmi, Nomor NPWP/Izin Usaha.
  - Metadata: Nomor Nota unik, Tanggal & Jam Transaksi, Nama Kasir/Sistem, Metode Pembayaran.
  - Rincian Produk: Seri iPhone, Varian Warna, Kapasitas, Nomor IMEI Unit, Status Garansi, Harga Satuan, Diskon, Total.
  - QR Code Validasi Keaslian Nota.
  - Syarat & Ketentuan Klaim Garansi Toko & Garansi Resmi.

---

### 5. Keamanan & Idempotensi Sistem
1. **Idempotensi Callback**:
   - Jika Midtrans mengirim webhook berulang kali untuk status yang sama, sistem memeriksa apakah pesanan sudah berstatus `paid`. Jika ya, sistem tidak akan mengirim pesan notifikasi ganda atau merusak stok inventaris.
2. **Pengurangan Stok Otomatis**:
   - Pengurangan kuantitas stok varian dieksekusi menggunakan database transaction (`DB::transaction`) saat status pembayaran terkonfirmasi `paid` guna mencegah *race conditions* atau *overselling*.
3. **Role-Based Access Control (RBAC) & Arsitektur Login Terpisah (Fase 14, 15, & 16)**:
   - Diterapkan melalui `RoleMiddleware` dan 3 pengontrol autentikasi mandiri yang memisahkan pintu masuk (*login pathway*) berdasarkan role:

| Fase | Peran (Role) | Endpoint Login & Register | Controller Penangan | Validasi & Isolasi Role | Target Redirect Utama |
|---|---|---|---|---|---|
| **Fase 14** | **Admin Operasional** (`admin`) | `GET/POST /admin/login`<br>`POST /admin/logout` | `AdminAuthController` | Wajib `role === 'admin'` (Menolak `customer` & `owner` di pintu ini; tanpa Google OAuth & tanpa Register) | `/admin/dashboard` |
| **Fase 15** | **Pemilik Usaha** (`owner`) | `GET/POST /owner/login`<br>`POST /owner/logout` | `OwnerAuthController` | Wajib `role === 'owner'` (Menolak `admin` & `customer` di pintu ini; tanpa Google OAuth & tanpa Register) | `/owner/dashboard` |
| **Fase 16** | **Pelanggan** (`customer`) | `GET/POST /login`<br>`GET/POST /register`<br>`GET /auth/google` | `AuthController` & `GoogleAuthController` | Wajib `role === 'customer'` (Menolak `admin` & `owner` di pintu publik pelanggan; dilengkapi Register mandiri & Google OAuth) | `/` (atau halaman checkout/intended) |

   - **Contextual Guest Redirection pada `RoleMiddleware`**:
     - Pengguna belum login (*guest*) yang mengakses `/admin/*` diarahkan ke `route('admin.login')` (`/admin/login`).
     - Pengguna belum login (*guest*) yang mengakses `/owner/*` diarahkan ke `route('owner.login')` (`/owner/login`).
     - Pengguna belum login (*guest*) yang mengakses `/customer/*` atau `/checkout/*` diarahkan ke `route('login')` (`/login`).

---

### 6. Arsitektur Frontend & Design System v2.0 (Mode ATM Digimap — `redesign.md`)

Mengacu pada spesifikasi di `projekiphone/redesign.md`, lapisan presentasi (*Presentation Layer*) bertransformasi dari tema gelap ke **Digimap-Inspired Daylight Minimalist & Frosted White UI** (`https://www.digimap.co.id/`):

#### 6.1. Struktur Token CSS & Lapisan Visual (`resources/css/`)
- **Palet Daylight Retail**:
  - `--dm-canvas: #FFFFFF` (Latar utama halaman)
  - `--dm-surface: #F5F5F7` (Permukaan kartu produk, bento promo, input form, & panel spesifikasi)
  - `--dm-glass-white: rgba(255, 255, 255, 0.84)` dengan `backdrop-filter: blur(20px)` (Sticky Header, Sticky Bottom Buy Bar, & Modal Overlay)
  - `--dm-text-primary: #1D1D1F`, `--dm-text-secondary: #6E6E73`, `--dm-border: #D2D2D7`, `--dm-blue-cta: #0071E3`
- **Kompatibilitas Utility Class**:
  - Class `.glass-card`, `.glass-navbar`, `.glass-button`, dan `.glass-modal` diperbarui secara terpusat agar merender permukaan *Clean White & Warm Gray* dengan *hairline border* halus (`1px solid #E5E5EA`) dan bayangan lembut (`0 4px 20px rgba(0, 0, 0, 0.04)`).

#### 6.2. Hierarki Layout & Komponen Blade (`resources/views/`)
```
resources/views/
├── layouts/
│   ├── customer.blade.php       <-- Top Promo Ticker + Sticky Daylight Header + Digimap Footer
│   └── admin.blade.php          <-- Digimap Enterprise Clean Light Sidebar & Topbar
├── auth/
│   ├── admin-login.blade.php    <-- Fase 14: Halaman Login Mandiri Admin Operasional (/admin/login)
│   ├── owner-login.blade.php    <-- Fase 15: Halaman Login Mandiri Eksekutif Owner (/owner/login)
│   ├── login.blade.php          <-- Fase 16: Halaman Login Mandiri Pelanggan + Google OAuth (/login)
│   └── register.blade.php       <-- Fase 16: Halaman Registrasi Mandiri Pelanggan (/register)
├── components/
│   ├── customer/                <-- Fase 25: product-card, spec-badge, button, color-swatch, battery-health-meter, section-header
│   ├── admin/                   <-- Fase 17: page-header, stat-card, card, empty-state, status-badge
│   ├── product-card.blade.php   <-- Legacy wrapper kompatibilitas
│   ├── glass-card.blade.php     <-- Kontainer permukaan putih/abu terang bersih
│   ├── glass-button.blade.php   <-- Pill/Rounded CTA (#0071E3 & #1D1D1F)
│   ├── order-badge.blade.php    <-- Semantic badges kontras tinggi di atas latar putih
│   └── battery-meter.blade.php  <-- Indikator Battery Health untuk unit Second Like New
├── admin/                       <-- Fase 17–20 & 24: dashboard, products/*, orders/*, trade-ins/*, settings
├── owner/                       <-- Fase 21–23: dashboard (+ partials/*), financial-report, staff/index
└── customer/
    ├── home.blade.php           <-- Fase 26: Hero Offer Flagship + Series Icon Ribbon + Bento Grid
    ├── catalog.blade.php        <-- Fase 27: Macrostructure Catalogue (11) + Dual-Condition Filter
    ├── pdp.blade.php            <-- Fase 28: Macrostructure Split Studio (15) + Configurator + Sticky Buy Bar
    ├── cart.blade.php           <-- Fase 29: Spec Sheet Item Ledger & Estimasi Potongan Trade-In
    ├── checkout.blade.php       <-- Fase 29: Two-Column Distraction-Free + Midtrans Snap Modal
    ├── trade-in.blade.php       <-- Fase 30: 4-Step Narrative Workflow Wizard Tukar Tambah
    ├── trade-in-detail.blade.php<-- Fase 30: Status & Timeline Inspeksi Unit Trade-In
    ├── orders.blade.php         <-- Fase 31: Buku Besar Transaksi Customer & Tracking Resi
    ├── checkout-success.blade.php<-- Fase 31: Bukti Pembayaran Lunas & Tombol Unduh Nota PDF
    └── utilities.blade.php      <-- Cek IMEI Kemenperin & Garansi Resmi Unit
```

#### 6.3. Hallmark Luxury / Editorial Design System (Fase 17–24)
Lapisan presentasi panel Admin Operasional dan Pemilik Usaha (Owner) ditransformasi menggunakan kerangka kerja **Hallmark Redesign** dengan tone **Luxury / Editorial** (genre: `editorial`). Desain memadukan ketenangan buku besar retail mewah (*trade ledger*), kertas limewash, tipografi display serif berbobot ringan, serta disiplin ketat aturan *The Floor* dari Hallmark.

**a. Spesifikasi Token CSS (`resources/css/admin.css` & `tokens.css`)**
Seluruh warna, tipografi, dan spasi diwajibkan melewati token CSS deklarasi `:root` tanpa nilai literal di dalam view:

```css
:root {
  /* Palet Kertas & Permukaan (Limewash & Cotton Stock) */
  --adm-paper: oklch(96% 0.008 80);        /* Kanvas limewash alabaster hangat (#FBFBFA) */
  --adm-paper-subtle: oklch(94% 0.010 78); /* Seksi sekunder / tabel background (#F5F4F0) */
  --adm-surface: #FFFFFF;                  /* Permukaan kartu cotton card stock */
  
  /* Tinta & Keterbacaan (Obsidian & Pencilled Slate) */
  --adm-ink: oklch(19% 0.024 52);          /* Tinta utama judul dan angka (#141413) */
  --adm-ink-muted: oklch(47% 0.014 60);    /* Teks sekunder pencilled (#6B6864) */
  --adm-ink-faint: oklch(65% 0.010 65);    /* Label wall-text dan caption (#9E9B95) */
  
  /* Garis Pembatas (Hairlines & Double Rules) */
  --adm-rule-hair: 1px solid oklch(88% 0.008 75); /* Pembatas baris hairline halus (#E5E2DC) */
  --adm-rule-head: 1px solid var(--adm-ink);      /* Garis kepala seksi / tabel */
  --adm-rule-double: 3px double var(--adm-ink);   /* Garis ganda buku kas master ledger */
  
  /* Aksen Tunggal & Fokus */
  --adm-accent: oklch(41% 0.075 248);      /* Cool Slate Titanium (#2C353D) */
  --adm-accent-soft: oklch(93% 0.02 248);  /* Latar badge tenang / hover row */
  --adm-focus: var(--adm-accent);          /* Ring fokus keyboard :focus-visible */
  
  /* Tipografi Editorial */
  --adm-font-display: "Sentient", "Playfair Display", Georgia, serif;
  --adm-font-body: "Hanken Grotesk", "Inter", -apple-system, sans-serif;
  --adm-tracking-display: -0.018em;
  --adm-display-weight: 300;               /* Bobot ringan serif elegan */
  
  /* Geometri & Sudut */
  --adm-radius: 2px;                       /* Sudut presisi ala buku cetak, tanpa sudut bulat balon */
  --adm-shadow: 0 1px 3px rgba(20, 20, 19, 0.04);
}
```

**b. Kelas Utility Utama (`resources/css/admin.css`)**

| Kelas | Karakteristik Visual Editorial & Fungsinya |
|---|---|
| `.adm-page` | Kontainer utama halaman, `max-width: 1440px`, padding lega, terikat `overflow-x: clip` |
| `.adm-page-header` | Kepala halaman editorial: judul serif display (`--adm-font-display`), subjudul satu kalimat terukur |
| `.adm-card` | Kartu *cotton stock* putih bersih dengan garis pembatas *hairline* `1px`, radius `2px`, tanpa bayangan tebal |
| `.adm-stat` | Panel *Statement Number*: angka moneter/volume serif ukuran besar, label pencilled untracked di bawahnya |
| `.adm-toolbar` | Baris kontrol bergaris tipis, pemilih status pill minimalis, dan kolom pencarian tanpa ornamen |
| `.adm-table` | Format *Master Trade Ledger*: kepala tabel bergaris ganda (`--adm-rule-double`), baris bergaris tipis, hover wash lembut |
| `.adm-num` | Angka moneter dan kuantitas rata kanan dengan font tubuh `tabular-nums` |
| `.adm-field`, `.adm-input` | Kontrol form dengan garis batas presisi 1px, state focus ring seketika (`:focus-visible`) |
| `.adm-badge` | Badge stempel (*editorial stamp*): berbingkai halus 1px, latar lembut monokromatik/semantik tenang |
| `.adm-empty` | State kosong bersahaja dengan deskripsi singkat tanpa ilustrasi kartun generik |

**c. Komponen Reusable Blade (`resources/views/components/admin/`)**
1. `<x-admin.page-header>`: Slot judul serif, subjudul satu kalimat terukur, dan aksi bergaris tinta (*hairline-outlined controls*).
2. `<x-admin.stat-card>`: Menampilkan angka metrik format *Statement Figure* bersahaja yang jujur dari database.
3. `<x-admin.card>`: Kontainer permukaan kertas bertekstur halus untuk membungkus konten formulir atau tabel.
4. `<x-admin.table>`: Tabel data dengan struktur *Trade Ledger* berstandar akuntansi presisi.
5. `<x-admin.status-badge>`: Label status transaksi (*paid*, *processing*, *shipped*, *completed*, *cancelled*) dengan gaya cap stempel kurasi.
6. `<x-admin.empty-state>`: Pesan ketiadaan data yang ringkas dan faktual.

**d. Tata Kelola Layout (`layouts/admin.blade.php`)**
- **Pemuatan Aset Font Eksekutif**:
  ```html
  <link href="https://api.fontshare.com/v2/css?f[]=sentient@300,400&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
  ```
- **Masthead Topbar**: Latar limewash dengan garis bawah 1px, jam operasional lokal presisi, breadcrumb ringan, dan indikator status API Midtrans & Fonnte berbasis data riil `$shell`.
- **Editorial Sidebar (Lebar 260px)**: Navigasi vertikal dengan garis pembatas halus, nomor antrean pesanan & trade-in ditampilkan sebagai nomor edisi (*pencilled edition count*), tanpa icon berwarna-warni norak.
- **Transisi & Animasi**: Bebas dari `transition: all`; hanya transisi properti terukur (`opacity`, `transform`, `background-color`) maksimal 150ms dengan fallback `@media (prefers-reduced-motion: reduce)`.

**e. Pola Halaman Baku**
`Page Header Editorial` $\rightarrow$ `Statement Stat Row` (opsional) $\rightarrow$ `Toolbar / Ledger Tabs` $\rightarrow$ `Trade Ledger Table / Cotton Form Card`. Diterapkan konsisten di:
- Admin: `/admin/dashboard`, `/admin/products/*`, `/admin/orders/*`, `/admin/trade-ins/*`, `/admin/settings`.
- Owner: `/owner/dashboard`, `/owner/financial-report`, `/owner/staff`.

#### 6.4. Fitur Interaktif Frontend (JavaScript / Vanilla DOM)
1. **Dynamic Variant & Price Switcher (PDP & Product Card)**:
   - Mengubah gambar aktif saat *Color Swatch* diklik dan memperbarui harga utama serta nominal simulasi cicilan per bulan secara instan saat *Storage Pill* (`128GB`–`1TB`) dipilih.
2. **Inline Trade-In Price Slasher (PDP)**:
   - Menghitung harga bersih (*Net Payable Price*) di halaman produk ketika pelanggan memilih model iPhone lama mereka sebagai tukar tambah.
3. **Sticky Bottom Purchase Bar**:
   - Menggunakan `IntersectionObserver` / scroll listener untuk menampilkan bar ringkasan pembelian di bagian bawah layar saat tombol beli utama di kolom kanan tidak terlihat di *viewport*.

---

#### 6.5. Hallmark Customer Storefront Design System (Fase 25–32)
Etalase belanja pelanggan (*Customer Storefront*) ditransformasi menggunakan kerangka kerja **Hallmark Redesign** dengan estetika **Luxury Retail & Studio Precision** (genre: `editorial` / `modern-minimal` hybrid). Desain menggabungkan ketenangan etalase perangkat keras fisik Apple, anatomi transaksi belanja jujur (*job: buy* dari `references/anatomy.md`), rasio ~80 kata per layar, dan penegakan ketat 16 aturan *The Floor* dari Hallmark.

**a. Spesifikasi Token CSS (`resources/css/customer.css`)**
Seluruh warna, tipografi, dan spasi diwajibkan melewati token CSS deklarasi `:root` tanpa nilai literal di dalam view:

```css
:root {
  /* Palet Kanvas Studio & Kartu Cotton Stock */
  --cust-paper: oklch(97.2% 0.006 215);        /* Kanvas limewash alabaster hangat (#FBFBFA) */
  --cust-paper-subtle: oklch(94.5% 0.008 215); /* Permukaan sekunder / strip pemisah (#F5F4F0) */
  --cust-surface: #FFFFFF;                     /* Kartu cotton stock bersih untuk produk & form */
  --cust-surface-elevated: #FFFFFF;            /* Permukaan elevasi dropdown & modal */
  --cust-glass: rgba(255, 255, 255, 0.88);     /* Kaca daylight sticky header & buy bar (blur 20px) */

  /* Tinta & Keterbacaan (Obsidian Pekat & Slate) */
  --cust-ink: oklch(16% 0.032 250);            /* Tinta judul utama & harga rupiah (#141413) */
  --cust-ink-2: oklch(28% 0.020 248);          /* Teks paragraf spesifikasi & nama unit (#2D2F33) */
  --cust-ink-muted: oklch(47% 0.014 60);       /* Teks sekunder, deskripsi varian (#6B6864) */
  --cust-ink-faint: oklch(65% 0.010 65);       /* Label wall-text, caption garansi (#9E9B95) */

  /* Garis Pembatas (Hairlines & Studio Rules) */
  --cust-rule-hair: 1px solid oklch(88% 0.008 75);  /* Garis pemisah hairline kartu (#E5E2DC) */
  --cust-rule-head: 1px solid var(--cust-ink);       /* Garis kepala seksi / kategori */
  --cust-rule-double: 3px double var(--cust-ink);    /* Garis ganda ringkasan checkout / nota */

  /* Sinyal Aksen & Fokus Interaktif */
  --cust-accent: oklch(41% 0.075 248);         /* Cool Slate Titanium (#2C353D) */
  --cust-signal: #0071E3;                      /* Apple Retail Blue untuk CTA primer Beli Sekarang */
  --cust-signal-hover: #0066CC;
  --cust-focus: var(--cust-signal);            /* Ring fokus keyboard :focus-visible instan */

  /* Status Semantik Kurasi (Tanpa Warna Terang Norak) */
  --cust-status-official-bg: oklch(94% 0.02 245);  --cust-status-official-text: oklch(35% 0.08 245);
  --cust-status-healthy-bg:  oklch(94% 0.03 145);  --cust-status-healthy-text:  oklch(35% 0.08 145);
  --cust-status-promo-bg:    oklch(94% 0.04 45);   --cust-status-promo-text:    oklch(38% 0.12 45);

  /* Tipografi Studio & Moneter Tabular */
  --cust-font-display: "Sentient", "Playfair Display", Georgia, serif;
  --cust-font-body: "Hanken Grotesk", "Inter", -apple-system, sans-serif;
  --cust-font-mono: "Geist Mono", ui-monospace, monospace;
  --cust-tracking-display: -0.020em;
  --cust-display-weight: 300;                  /* Serif berbobot ringan, berwibawa tenang */

  /* Geometri & Sudut */
  --cust-radius: 4px;                          /* Sudut presisi ala cetak studio hardware */
  --cust-radius-pill: 999px;                   /* Pill untuk selektor warna & kapasitas */
  --cust-shadow: 0 1px 3px rgba(20, 20, 19, 0.05);
}
```

**b. Kelas Utility Utama Customer (`resources/css/customer.css`)**

| Kelas | Karakteristik Visual & Penerapannya pada UI Pelanggan |
|---|---|
| `.cust-page` | Kontainer utama seluruh halaman etalase, `max-width: 1440px`, terikat `overflow-x: clip` |
| `.cust-navbar` | Sticky daylight header dengan latar `--cust-glass`, `backdrop-filter: blur(20px)`, border bawah 1px hairline |
| `.cust-card` | Kartu produk berkanvas cotton stock (`--cust-surface`), border `1px hairline`, hover elevasi ringan 1px |
| `.cust-hero` | Hero stage flagship: headline serif berwibawa ringan, 1 kalimat terukur, aksi ganda, tampak di 1280x800 tanpa scroll |
| `.cust-gallery` | Galeri studio sticky ber-rasio presisi, thumbnail bertata letak mat studio tanpa mockup ponsel fiktif |
| `.cust-num` | Nilai moneter rupiah dan kuantitas berformat rata kanan dengan font `tabular-nums` |
| `.cust-swatch` | Tombol titik warna bodi aktual Apple (Natural Titanium, Desert Titanium, White, Black) ber-ring fokus presisi |
| `.cust-pill` | Tombol pill selektor kapasitas memori (128GB–1TB) bergaris batas halus dan state aktif tegas |
| `.cust-badge` | Badge stempel kurasi (*Resmi iBox*, *Second Grade A+*, *Battery Health 95%*) |
| `.cust-buybar` | Sticky Bottom Purchase Bar responsif dengan ringkasan varian aktif dan tombol beli langsung |

**c. Pemetaan Makrostruktur per Halaman (`references/macrostructures.md`)**

1. **Beranda (`customer/home.blade.php`)**:
   - Makrostruktur: **Bento Grid (01)** & **Catalogue (11)**.
   - Hero: *Offer as Hero* (iPhone 16 Pro Flagship) dengan rasio ~80 kata per layar, headline 7 kata, subteks 12 kata, tombol aksi ganda (*Beli Sekarang* & *Simulasi Trade-In*).
   - Visual Ribbon: Baris ikon seri iPhone horizontal.
   - Curated Bento Grid: 3 pilar layanan toko (Trade-In 15 Menit, Garansi Resmi vs Second QC 32-Titik, Click & Collect Gerai Fisik).
2. **Katalog Produk (`customer/catalog.blade.php`)**:
   - Makrostruktur: **Catalogue (11)** — *Visual Index of Inventory*.
   - Dual-Condition Segmented Control: [Semua] | [Unit Baru Garansi Resmi] | [Second Like New Teruji].
   - Toolbar Filter: Seri, Kapasitas, Rentang Harga, Ketersediaan Stok.
   - Grid 4 Kolom: Kartu produk cotton stock ber-swatch warna, indikator BH, dan harga tabular.
3. **Halaman Detail Produk / PDP (`customer/pdp.blade.php`)**:
   - Makrostruktur: **Split Studio (15)**.
   - Kolom Kiri: *Sticky Studio Gallery* dengan multi-angle device photography, zoom detail, dan rasio foto asli.
   - Kolom Kanan: *Step-by-Step Configurator* (Kondisi -> Varian Warna -> Kapasitas Penyimpanan) dengan pembaruan harga seketika.
   - Transparency Trust Panel: Data riil *Battery Health* meter, nomor IMEI terdaftar Kemenperin, status kelulusan QC 32-titik.
   - Dual Price Slasher: Estimasi cicilan per bulan dan potongan harga tukar tambah langsung (*inline trade-in estimator*).
   - Sticky Bottom Purchase Bar: Ringkasan dinamis varian aktif yang menempel di bawah layar.
4. **Keranjang & Checkout Midtrans (`customer/cart.blade.php` & `customer/checkout.blade.php`)**:
   - Makrostruktur: *Spec Sheet & Master Ledger Checkout*.
   - Lembar keranjang: Rincian item tabular, voucher diskon, dan subtotal bersih.
   - Checkout 2 Kolom Bebas Distraksi: Form kurir pengiriman (JNE/SiCepat/J&T) vs *Click & Collect* di gerai fisik toko, ringkasan biaya bergaris ganda (`--cust-rule-double`), serta trigger popup modal Midtrans Snap ber-focus ring instan.
5. **Wizard Tukar Tambah (`customer/trade-in.blade.php` & `trade-in-detail.blade.php`)**:
   - Makrostruktur: **Narrative Workflow (14)** / **Workbench (05)**.
   - 4 Langkah Transparan: Model iPhone Lama -> Kapasitas & BH -> Kondisi Fisik & Kelengkapan -> Estimasi Nilai Tukar Tambah Instan.
   - Opsi Klaim: Voucher potongan belanja langsung di keranjang atau transfer rekening bank customer.
6. **Riwayat Pesanan & Nota PDF (`customer/orders.blade.php` & `checkout-success.blade.php`)**:
   - Halaman konfirmasi pembayaran lunas seketika (*Settlement Receipt*).
   - Master Ledger pesanan pelanggan: Tabel transaksi historis dengan stempel status, tracking resi ekspedisi, dan tombol unduh faktur PDF DomPDF resmi ber-QR Code.

**d. Penegakan 16 Aturan "The Floor" Hallmark pada Customer Storefront**
- **F1**: 100% warna dan font dideklarasikan via token `:root` di `customer.css`.
- **F2**: Bebas dari `transition: all`; hanya transisi properti terukur (`transform`, `opacity`, `background-color`) maksimal 150ms.
- **F4**: Terikat `overflow-x: clip` pada kontainer root (tidak ada `hidden` atau `100vw`).
- **F6**: `:focus-visible` instan pada setiap tombol, input formulir, dot warna, dan link navigasi.
- **F7**: Fallback `@media (prefers-reduced-motion: reduce)` pada setiap interaksi gerak.
- **F8**: Dilarang memuat angka ulasan palsu, rating bintang buatan, logo wall partner fiktif, atau testimoni karangan; hanya menampilkan data nyata inventaris dan transaksi.
- **F9**: Label dan badge menggunakan huruf sentence case atau lower case untracked (tidak ada tracked capitals atau small caps).
- **F11**: Dilarang menggunakan kartu bersarang (*card inside a card*), gradien ungu-ke-biru, atau emotikon sebagai ikon.
- **F12 & F13**: Hero headline, kalimat pengantar, dan tombol aksi tampak utuh pada resolusi 1280x800 tanpa scroll; rasio ~80 kata per layar.
- **F14**: Kontainer etalase terpusat dan dibatasi maksimal 1440px / 1920px (gutters melebar pada layar lebar, grid tidak melar tak berbatas).
