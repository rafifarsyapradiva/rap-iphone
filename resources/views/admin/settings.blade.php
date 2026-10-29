@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem & Integrasi API — Rap-iPhone Admin')
@section('page_title', 'Konfigurasi & Status API')

@section('content')
<div class="space-y-6">

    <!-- 1. Page Header -->
    <x-admin.page-header
        title="Konfigurasi Sistem & Integrasi API"
        subtitle="Monitoring status koneksi Payment Gateway Midtrans, WhatsApp Fonnte, SMTP Mailer, dan Google OAuth 2.0"
    >
        <x-slot:actions>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Arsitektur Service Layer Aktif</span>
            </span>
        </x-slot:actions>
    </x-admin.page-header>

    <!-- 2. Integration Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Midtrans -->
        <div class="adm-card p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0066CC] flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ $integrations['midtrans']['name'] }}</h2>
                        <span class="text-[11px] text-slate-500">Payment Gateway Snap</span>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $integrations['midtrans']['mode'] }}
                </span>
            </div>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-slate-500 block mb-1">Webhook Endpoint:</span>
                    <code class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-800 font-mono text-[11px] block overflow-x-auto">
                        {{ $integrations['midtrans']['webhook_url'] }}
                    </code>
                </div>
                <div class="flex items-center gap-2 pt-1 text-[11px] text-emerald-700 font-medium">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                    <span>Verifikasi SHA-512 Signature Hash &amp; Idempotency Guard: Aktif</span>
                </div>
            </div>
        </div>

        <!-- Fonnte -->
        <div class="adm-card p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ $integrations['fonnte']['name'] }}</h2>
                        <span class="text-[11px] text-slate-500">Notifikasi WhatsApp Otomatis</span>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $integrations['fonnte']['configured'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                    {{ $integrations['fonnte']['configured'] ? 'Live Token' : 'Sandbox Simulation' }}
                </span>
            </div>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-slate-500 block mb-1">Endpoint API:</span>
                    <code class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-800 font-mono text-[11px] block overflow-x-auto">
                        {{ $integrations['fonnte']['endpoint'] }}
                    </code>
                </div>
                <div class="flex items-center gap-2 pt-1 text-[11px] text-emerald-700 font-medium">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                    <span>4 Template Dinamis (Order, Paid, Shipped, Trade-In): Aktif</span>
                </div>
            </div>
        </div>

        <!-- SMTP Mailer -->
        <div class="adm-card p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ $integrations['smtp']['name'] }}</h2>
                        <span class="text-[11px] text-slate-500">Pengiriman Email &amp; Faktur PDF</span>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                    Driver: {{ strtoupper($integrations['smtp']['driver']) }}
                </span>
            </div>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-slate-500 block mb-1">Host SMTP:</span>
                    <code class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-800 font-mono text-[11px] block overflow-x-auto">
                        {{ $integrations['smtp']['host'] }}
                    </code>
                </div>
                <div class="flex items-center gap-2 pt-1 text-[11px] text-emerald-700 font-medium">
                    <i data-lucide="file-check-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                    <span>Lampiran Otomatis Nota PDF (DomPDF A4 + QR SVG): Aktif</span>
                </div>
            </div>
        </div>

        <!-- Google OAuth -->
        <div class="adm-card p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ $integrations['google']['name'] }}</h2>
                        <span class="text-[11px] text-slate-500">Single Sign-On (SSO)</span>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $integrations['google']['configured'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                    {{ $integrations['google']['configured'] ? 'Connected' : 'Ready (.env)' }}
                </span>
            </div>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-slate-500 block mb-1">Callback URI:</span>
                    <code class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-800 font-mono text-[11px] block overflow-x-auto">
                        {{ $integrations['google']['redirect'] }}
                    </code>
                </div>
                <div class="flex items-center gap-2 pt-1 text-[11px] text-emerald-700 font-medium">
                    <i data-lucide="users" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                    <span>Sinkronisasi otomatis akun Customer 1-Klik: Aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Recent Notification Logs Table -->
    <x-admin.card
        title="Log Pengiriman Notifikasi Terakhir (WA &amp; Email)"
        subtitle="Audit jejak pengiriman pesan transaksi pelanggan secara real-time"
    >
        @if($recentLogs->isEmpty())
            <x-admin.empty-state
                icon="bell-off"
                title="Belum Ada Log Notifikasi"
                description="Belum ada riwayat pengiriman notifikasi yang tercatat di sistem."
            />
        @else
            <div class="overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Channel</th>
                            <th>Penerima</th>
                            <th>Event</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogs as $log)
                            <tr>
                                <td class="text-slate-500 whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $log->channel === 'whatsapp' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                        <i data-lucide="{{ $log->channel === 'whatsapp' ? 'message-square' : 'mail' }}" class="w-3 h-3"></i>
                                        <span>{{ $log->channel }}</span>
                                    </span>
                                </td>
                                <td class="font-mono text-slate-700">{{ $log->recipient }}</td>
                                <td class="text-slate-800 font-medium">{{ $log->event_name }}</td>
                                <td>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $log->status === 'sent' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ strtoupper($log->status) }}
                                    </span>
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
