@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem & Integrasi API — Rap-iPhone Admin')
@section('page_title', 'Konfigurasi & Status API')

@section('content')
<div class="adm-page">

    <!-- 1. Page Header -->
    <x-admin.page-header
        title="Konfigurasi Sistem & Integrasi API"
        subtitle="Monitoring status koneksi Payment Gateway Midtrans, WhatsApp Fonnte, SMTP Mailer, dan Google OAuth 2.0"
        icon="sliders">
        <x-slot:actions>
            <span class="adm-badge adm-tone-green">
                <span class="adm-badge-dot"></span>
                <span>Arsitektur Service Layer Aktif</span>
            </span>
        </x-slot:actions>
    </x-admin.page-header>

    <!-- 2. Integration Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Midtrans -->
        <div class="adm-card p-5 space-y-4">
            <div class="flex items-center justify-between pb-3" style="border-bottom:1px solid var(--adm-hairline)">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded flex items-center justify-center" style="background:var(--adm-paper);color:var(--adm-accent);border:1px solid var(--adm-hairline)">
                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold" style="color:var(--adm-ink)">{{ $integrations['midtrans']['name'] }}</h2>
                        <span class="text-xs" style="color:var(--adm-muted)">Payment Gateway Snap</span>
                    </div>
                </div>
                <x-admin.status-badge tone="blue" :label="$integrations['midtrans']['mode']" :dot="false" />
            </div>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="block mb-1" style="color:var(--adm-muted)">Webhook Endpoint:</span>
                    <code class="px-2.5 py-1 rounded font-mono text-[11px] block overflow-x-auto" style="background:var(--adm-paper);border:1px solid var(--adm-hairline);color:var(--adm-ink)">
                        {{ $integrations['midtrans']['webhook_url'] }}
                    </code>
                </div>
                <div class="flex items-center gap-2 pt-1 text-xs font-medium" style="color:var(--adm-moss)">
                    <i data-lucide="shield-check" class="w-4 h-4 shrink-0"></i>
                    <span>Verifikasi SHA-512 Signature Hash &amp; Idempotency Guard: Aktif</span>
                </div>
            </div>
        </div>

        <!-- Fonnte -->
        <div class="adm-card p-5 space-y-4">
            <div class="flex items-center justify-between pb-3" style="border-bottom:1px solid var(--adm-hairline)">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded flex items-center justify-center" style="background:var(--adm-paper);color:var(--adm-moss);border:1px solid var(--adm-hairline)">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold" style="color:var(--adm-ink)">{{ $integrations['fonnte']['name'] }}</h2>
                        <span class="text-xs" style="color:var(--adm-muted)">Notifikasi WhatsApp Otomatis</span>
                    </div>
                </div>
                <x-admin.status-badge :tone="$integrations['fonnte']['configured'] ? 'green' : 'amber'" :label="$integrations['fonnte']['configured'] ? 'Live Token' : 'Sandbox Simulation'" :dot="false" />
            </div>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="block mb-1" style="color:var(--adm-muted)">Endpoint API:</span>
                    <code class="px-2.5 py-1 rounded font-mono text-[11px] block overflow-x-auto" style="background:var(--adm-paper);border:1px solid var(--adm-hairline);color:var(--adm-ink)">
                        {{ $integrations['fonnte']['endpoint'] }}
                    </code>
                </div>
                <div class="flex items-center gap-2 pt-1 text-xs font-medium" style="color:var(--adm-moss)">
                    <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
                    <span>4 Template Dinamis (Order, Paid, Shipped, Trade-In): Aktif</span>
                </div>
            </div>
        </div>

        <!-- SMTP Mailer -->
        <div class="adm-card p-5 space-y-4">
            <div class="flex items-center justify-between pb-3" style="border-bottom:1px solid var(--adm-hairline)">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded flex items-center justify-center" style="background:var(--adm-paper);color:var(--adm-accent);border:1px solid var(--adm-hairline)">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold" style="color:var(--adm-ink)">{{ $integrations['smtp']['name'] }}</h2>
                        <span class="text-xs" style="color:var(--adm-muted)">Pengiriman Email &amp; Faktur PDF</span>
                    </div>
                </div>
                <x-admin.status-badge tone="gray" :label="'Driver: ' . strtoupper($integrations['smtp']['driver'])" :dot="false" />
            </div>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="block mb-1" style="color:var(--adm-muted)">Host SMTP:</span>
                    <code class="px-2.5 py-1 rounded font-mono text-[11px] block overflow-x-auto" style="background:var(--adm-paper);border:1px solid var(--adm-hairline);color:var(--adm-ink)">
                        {{ $integrations['smtp']['host'] }}
                    </code>
                </div>
                <div class="flex items-center gap-2 pt-1 text-xs font-medium" style="color:var(--adm-moss)">
                    <i data-lucide="file-check-2" class="w-4 h-4 shrink-0"></i>
                    <span>Lampiran Otomatis Nota PDF (DomPDF A4 + QR SVG): Aktif</span>
                </div>
            </div>
        </div>

        <!-- Google OAuth -->
        <div class="adm-card p-5 space-y-4">
            <div class="flex items-center justify-between pb-3" style="border-bottom:1px solid var(--adm-hairline)">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded flex items-center justify-center" style="background:var(--adm-paper);color:var(--adm-accent);border:1px solid var(--adm-hairline)">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold" style="color:var(--adm-ink)">{{ $integrations['google']['name'] }}</h2>
                        <span class="text-xs" style="color:var(--adm-muted)">Single Sign-On (SSO)</span>
                    </div>
                </div>
                <x-admin.status-badge :tone="$integrations['google']['configured'] ? 'green' : 'blue'" :label="$integrations['google']['configured'] ? 'Connected' : 'Ready (.env)'" :dot="false" />
            </div>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="block mb-1" style="color:var(--adm-muted)">Callback URI:</span>
                    <code class="px-2.5 py-1 rounded font-mono text-[11px] block overflow-x-auto" style="background:var(--adm-paper);border:1px solid var(--adm-hairline);color:var(--adm-ink)">
                        {{ $integrations['google']['redirect'] }}
                    </code>
                </div>
                <div class="flex items-center gap-2 pt-1 text-xs font-medium" style="color:var(--adm-moss)">
                    <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                    <span>Sinkronisasi otomatis akun Customer 1-Klik: Aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Recent Notification Logs Table -->
    <x-admin.card
        title="Log Pengiriman Notifikasi Terakhir (WA &amp; Email)"
        subtitle="Audit jejak pengiriman pesan transaksi pelanggan secara real-time"
        flush
    >
        @if($recentLogs->isEmpty())
            <div class="p-8 text-center" style="color:var(--adm-muted)">
                <p class="text-sm">Belum Ada Log Notifikasi</p>
                <p class="text-xs mt-1">Belum ada riwayat pengiriman notifikasi yang tercatat di sistem.</p>
            </div>
        @else
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem">Waktu</th>
                            <th>Channel</th>
                            <th>Penerima</th>
                            <th>Event</th>
                            <th style="text-align:right;padding-right:1.5rem">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogs as $log)
                            <tr>
                                <td class="adm-cell-sub" style="padding-left:1.5rem">{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                                <td>
                                    <x-admin.status-badge :tone="$log->channel === 'whatsapp' ? 'green' : 'blue'" :label="strtoupper($log->channel)" :dot="false" />
                                </td>
                                <td class="adm-mono font-medium" style="color:var(--adm-ink)">{{ $log->recipient }}</td>
                                <td class="adm-cell-title">{{ $log->event_name }}</td>
                                <td style="text-align:right;padding-right:1.5rem">
                                    <x-admin.status-badge :tone="$log->status === 'sent' ? 'green' : 'rose'" :label="strtoupper($log->status)" :dot="false" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-admin.card>

</div>
@endsection
