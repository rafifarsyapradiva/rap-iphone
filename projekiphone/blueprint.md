# ARCHITECTURE & SYSTEM BLUEPRINT
## Platform E-Commerce Jual Beli iPhone (Rap-iPhone)

---

### 1. Diagram Arsitektur Sistem

```
+-----------------------------------------------------------------------------------+
|                                  CLIENT LAYER                                     |
|  +---------------------------+  +--------------------------+  +-----------------+  |
|  | Customer Portal           |  | Admin Operations         |  | Owner Dashboard |  |
|  | (Responsive Blade UI      |  | (Katalog, Pesanan,       |  | (Analitik Laba, |  |
|  |  + Glassmorphism CSS)     |  |  Trade-in, Resi Kurir)   |  |  Omzet, Rekap)  |  |
|  +---------------------------+  +--------------------------+  +-----------------+  |
+----------------------------------------+------------------------------------------+
                                         | HTTP / HTTPS Requests
                                         v
+-----------------------------------------------------------------------------------+
|                           LARAVEL 12 APPLICATION CORE                             |
|                                                                                   |
|  [ ROUTING & MIDDLEWARE ]                                                         |
|  + web.php, api.php                                                               |
|  + AuthMiddleware, RoleMiddleware (customer | admin | owner)                      |
|  + VerifyCsrfToken (kecuali webhook endpoint api/midtrans/callback)               |
|                                                                                   |
|  [ CONTROLLER LAYER ]                                                             |
|  + AuthController & GoogleAuthController (Socialite)                              |
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
│   │   │   ├── LoginController.php
│   │   │   └── GoogleAuthController.php  <-- Laravel Socialite
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
│   │   │   └── FinancialReportController.php
│   │   └── Api/
│   │       └── MidtransCallbackController.php  <-- Webhook Handler
│   └── Middleware/
│       └── RoleMiddleware.php                  <-- RBAC Guard
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

#### 4.1. Google Console OAuth 2.0 (Laravel Socialite)
- **Library**: `laravel/socialite`
- **Redirect URI**: `https://domain.com/auth/google/callback`
- **Logika Autentikasi**:
  1. Pengguna klik "Masuk dengan Google".
  2. Redirect ke Google Consent Screen.
  3. Callback menerima token dan profil: `email`, `name`, `id`, `avatar`.
  4. Cari user berdasarkan `google_id` atau `email`.
  5. Jika user sudah ada, sinkronkan `google_id` dan *login*. Jika belum, buat record baru dengan role `customer`.

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
3. **Role-Based Access Control (RBAC)**:
   - Diterapkan melalui `RoleMiddleware` pada level rute:
     - `/admin/*` hanya dapat diakses oleh user ber-role `admin` dan `owner`.
     - `/owner/*` (laporan keuangan, laba rugi, kontrol admin) khusus user ber-role `owner`.
     - `/customer/*` dan transaksi umum diperuntukkan bagi customer.
