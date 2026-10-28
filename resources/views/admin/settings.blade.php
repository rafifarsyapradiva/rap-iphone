@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem & Integrasi API — Rap-iPhone Admin')
@section('page_title', 'Konfigurasi & Status API')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Konfigurasi Sistem & Integrasi API</h1>
            <p class="text-xs text-[#8E8E93] mt-1">Monitoring status koneksi Payment Gateway Midtrans, WhatsApp Fonnte, SMTP Mailer, dan Google OAuth 2.0</p>
        </div>
        <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-[#34C759]/15 text-[#34C759] border border-[#34C759]/30">
            Arsitektur Service Layer Aktif
        </span>
    </div>

    <!-- Integration Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass-card p-6 rounded-3xl border border-white/10 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-white">{{ $integrations['midtrans']['name'] }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0071E3]/20 text-[#5AC8FA] border border-[#0071E3]/30">
                    {{ $integrations['midtrans']['mode'] }}
                </span>
            </div>
            <p class="text-xs text-[#8E8E93]">Webhook Endpoint: <code class="text-white font-mono">{{ $integrations['midtrans']['webhook_url'] }}</code></p>
            <p class="text-[11px] text-[#34C759]">Verifikasi SHA-512 Signature Hash & Idempotency Guard: Aktif</p>
        </div>

        <div class="glass-card p-6 rounded-3xl border border-white/10 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-white">{{ $integrations['fonnte']['name'] }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#34C759]/20 text-[#34C759] border border-[#34C759]/30">
                    {{ $integrations['fonnte']['configured'] ? 'Live Token' : 'Sandbox Simulation' }}
                </span>
            </div>
            <p class="text-xs text-[#8E8E93]">Endpoint API: <code class="text-white font-mono">{{ $integrations['fonnte']['endpoint'] }}</code></p>
            <p class="text-[11px] text-[#34C759]">4 Template Dinamis (Order, Paid, Shipped, Trade-In): Aktif</p>
        </div>

        <div class="glass-card p-6 rounded-3xl border border-white/10 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-white">{{ $integrations['smtp']['name'] }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-white">
                    Driver: {{ strtoupper($integrations['smtp']['driver']) }}
                </span>
            </div>
            <p class="text-xs text-[#8E8E93]">Host SMTP: <code class="text-white font-mono">{{ $integrations['smtp']['host'] }}</code></p>
            <p class="text-[11px] text-[#34C759]">Lampiran Otomatis Nota PDF (DomPDF A4 + QR SVG): Aktif</p>
        </div>

        <div class="glass-card p-6 rounded-3xl border border-white/10 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-white">{{ $integrations['google']['name'] }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0071E3]/20 text-[#5AC8FA]">
                    {{ $integrations['google']['configured'] ? 'Connected' : 'Ready (.env)' }}
                </span>
            </div>
            <p class="text-xs text-[#8E8E93]">Callback URI: <code class="text-white font-mono">{{ $integrations['google']['redirect'] }}</code></p>
            <p class="text-[11px] text-[#34C759]">Sinkronisasi otomatis akun Customer 1-Klik: Aktif</p>
        </div>
    </div>

    <!-- Recent Notification Logs -->
    <div class="glass-card p-6 rounded-3xl border border-white/10 space-y-4">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Log Pengiriman Notifikasi Terakhir (WA & Email)</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-[#8E8E93] border-b border-white/10">
                    <tr>
                        <th class="py-2.5 pr-4">Waktu</th>
                        <th class="py-2.5 px-4">Channel</th>
                        <th class="py-2.5 px-4">Penerima</th>
                        <th class="py-2.5 px-4">Event</th>
                        <th class="py-2.5 pl-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recentLogs as $log)
                        <tr>
                            <td class="py-2.5 pr-4 text-[#8E8E93]">{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                            <td class="py-2.5 px-4 font-bold uppercase text-white">{{ $log->channel }}</td>
                            <td class="py-2.5 px-4 font-mono text-[#E5E5EA]">{{ $log->recipient }}</td>
                            <td class="py-2.5 px-4 text-[#5AC8FA]">{{ $log->event_name }}</td>
                            <td class="py-2.5 pl-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $log->status === 'sent' ? 'bg-[#34C759]/20 text-[#34C759]' : 'bg-[#FF453A]/20 text-[#FF453A]' }}">
                                    {{ strtoupper($log->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-[#8E8E93]">Belum ada riwayat pengiriman notifikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

