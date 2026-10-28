<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Nota / Faktur PDF Resmi Rap-iPhone (Phase 9)
|--------------------------------------------------------------------------
|
| Identitas toko, aturan masa garansi per tipe, serta syarat & ketentuan
| klaim garansi yang dicetak pada nota PDF dan label pengiriman.
| Seluruh identitas toko dapat dioverride melalui file .env (STORE_*).
|
*/

return [

    'store' => [
        'name' => env('STORE_NAME', 'RAP-iPHONE'),
        'tagline' => env('STORE_TAGLINE', 'Premium Apple Retail & Trade-In Partner'),
        'address' => env('STORE_ADDRESS', 'Jl. Jenderal Sudirman Kav. 52-53, SCBD, Jakarta Selatan 12190'),
        'whatsapp' => env('STORE_WHATSAPP', '+62 811-9000-8899'),
        'email' => env('STORE_EMAIL', 'official@rap-iphone.com'),
        'npwp' => env('STORE_NPWP', '09.254.881.4-012.000'),
        'nib' => env('STORE_NIB', '9120300491821'),
    ],

    /*
    | Masa garansi berdasarkan kolom products.warranty_type.
    | Tanggal mulai garansi = payments.payment_time (fallback orders.created_at).
    */
    'warranty' => [
        'resmi_ibox' => ['label' => 'Garansi Resmi Apple Indonesia (iBox)', 'months' => 12],
        'resmi_digimap' => ['label' => 'Garansi Resmi Apple Indonesia (Digimap)', 'months' => 12],
        'inter_all_operator' => ['label' => 'Garansi Toko - Inter (IMEI Terdaftar)', 'months' => 3],
        'toko' => ['label' => 'Garansi Toko Rap-iPhone', 'months' => 1],
    ],

    // Jaminan tukar unit baru bila ditemukan cacat pabrik.
    'replacement_days' => 7,

    // Status pesanan yang notanya boleh diunduh oleh customer.
    'downloadable_statuses' => ['paid', 'processing', 'shipped', 'completed'],

    // Status pesanan yang layak dicetak label pengirimannya.
    'label_statuses' => ['paid', 'processing', 'shipped'],

    // Batas maksimum pesanan per sekali cetak massal (menjaga memori DomPDF).
    'bulk_max' => 50,

    'terms' => [
        'Nota Faktur ini merupakan bukti pembelian yang sah dan wajib ditunjukkan saat mengajukan klaim garansi di Rap-iPhone maupun service center resmi.',
        'Unit baru (BNIB) bergaransi resmi Apple Indonesia mengikuti ketentuan Apple Authorized Service Provider (iBox / Digimap) selama masa garansi yang tertera.',
        'Jaminan tukar unit dalam :days hari sejak barang diterima apabila ditemukan cacat pabrik, dengan syarat menyertakan video unboxing utuh tanpa jeda serta kelengkapan & segel dalam kondisi asli.',
        'Garansi toko untuk unit second / inter mencakup kerusakan fungsi mesin (non-fisik) dan tidak mencakup baterai yang menurun secara wajar.',
        'Garansi gugur apabila terdapat kerusakan akibat cairan, benturan/jatuh, human error, modifikasi perangkat lunak (jailbreak), atau perbaikan di luar Rap-iPhone / service center resmi.',
    ],

];

