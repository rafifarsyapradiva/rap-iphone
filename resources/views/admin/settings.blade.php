@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem & Integrasi API — Rap-iPhone Admin')
@section('page_title', 'Konfigurasi & Status API')

@section('content')
<div class="adm-page space-y-6">

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
            <button type="button" onclick="testAllGateways()" class="adm-btn adm-btn-primary text-xs py-1.5 px-3">
                <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                <span>Uji Semua Koneksi</span>
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <!-- 2. Integration Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Midtrans -->
        <div class="adm-card p-5 space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3" style="border-bottom:1px solid var(--adm-hairline)">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded flex items-center justify-center shrink-0" style="background:var(--adm-paper);color:var(--adm-accent);border:1px solid var(--adm-hairline)">
                            <i data-lucide="credit-card" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold" style="color:var(--adm-ink)">{{ $integrations['midtrans']['name'] }}</h2>
                            <span class="text-xs" style="color:var(--adm-muted)">Payment Gateway Snap</span>
                        </div>
                    </div>
                    <div id="badge-midtrans">
                        <x-admin.status-badge tone="blue" :label="$integrations['midtrans']['mode']" :dot="false" />
                    </div>
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

            <!-- Probe Control Footer -->
            <div class="pt-3 flex items-center justify-between gap-3 text-xs" style="border-top:1px solid var(--adm-hairline)">
                <div class="truncate text-[11px]" id="status-midtrans" style="color:var(--adm-muted)">
                    {{ $integrations['midtrans']['message'] ?? 'Status terkonfigurasi' }}
                </div>
                <button type="button" id="btn-midtrans" onclick="testGateway('midtrans')" class="adm-btn adm-btn-secondary text-xs py-1 px-2.5 shrink-0">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5" id="icon-midtrans"></i>
                    <span>Uji Koneksi</span>
                </button>
            </div>
        </div>

        <!-- Fonnte -->
        <div class="adm-card p-5 space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3" style="border-bottom:1px solid var(--adm-hairline)">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded flex items-center justify-center shrink-0" style="background:var(--adm-paper);color:var(--adm-moss);border:1px solid var(--adm-hairline)">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold" style="color:var(--adm-ink)">{{ $integrations['fonnte']['name'] }}</h2>
                            <span class="text-xs" style="color:var(--adm-muted)">Notifikasi WhatsApp Otomatis</span>
                        </div>
                    </div>
                    <div id="badge-fonnte">
                        <x-admin.status-badge :tone="$integrations['fonnte']['configured'] ? 'green' : 'amber'" :label="$integrations['fonnte']['configured'] ? 'Live Token' : 'Sandbox Simulation'" :dot="false" />
                    </div>
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

            <!-- Probe Control Footer -->
            <div class="pt-3 flex items-center justify-between gap-3 text-xs" style="border-top:1px solid var(--adm-hairline)">
                <div class="truncate text-[11px]" id="status-fonnte" style="color:var(--adm-muted)">
                    {{ $integrations['fonnte']['message'] ?? 'Status terkonfigurasi' }}
                </div>
                <button type="button" id="btn-fonnte" onclick="testGateway('fonnte')" class="adm-btn adm-btn-secondary text-xs py-1 px-2.5 shrink-0">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5" id="icon-fonnte"></i>
                    <span>Uji Koneksi</span>
                </button>
            </div>
        </div>

        <!-- SMTP Mailer -->
        <div class="adm-card p-5 space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3" style="border-bottom:1px solid var(--adm-hairline)">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded flex items-center justify-center shrink-0" style="background:var(--adm-paper);color:var(--adm-accent);border:1px solid var(--adm-hairline)">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold" style="color:var(--adm-ink)">{{ $integrations['smtp']['name'] }}</h2>
                            <span class="text-xs" style="color:var(--adm-muted)">Pengiriman Email &amp; Faktur PDF</span>
                        </div>
                    </div>
                    <div id="badge-smtp">
                        <x-admin.status-badge tone="gray" :label="'Driver: ' . strtoupper($integrations['smtp']['driver'])" :dot="false" />
                    </div>
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

            <!-- Probe Control Footer -->
            <div class="pt-3 flex items-center justify-between gap-3 text-xs" style="border-top:1px solid var(--adm-hairline)">
                <div class="truncate text-[11px]" id="status-smtp" style="color:var(--adm-muted)">
                    {{ $integrations['smtp']['message'] ?? 'Status terkonfigurasi' }}
                </div>
                <button type="button" id="btn-smtp" onclick="testGateway('smtp')" class="adm-btn adm-btn-secondary text-xs py-1 px-2.5 shrink-0">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5" id="icon-smtp"></i>
                    <span>Uji Koneksi</span>
                </button>
            </div>
        </div>

        <!-- Google OAuth -->
        <div class="adm-card p-5 space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3" style="border-bottom:1px solid var(--adm-hairline)">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded flex items-center justify-center shrink-0" style="background:var(--adm-paper);color:var(--adm-accent);border:1px solid var(--adm-hairline)">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold" style="color:var(--adm-ink)">{{ $integrations['google']['name'] }}</h2>
                            <span class="text-xs" style="color:var(--adm-muted)">Single Sign-On (SSO)</span>
                        </div>
                    </div>
                    <div id="badge-google">
                        <x-admin.status-badge :tone="$integrations['google']['configured'] ? 'green' : 'blue'" :label="$integrations['google']['configured'] ? 'Connected' : 'Ready (.env)'" :dot="false" />
                    </div>
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

            <!-- Probe Control Footer -->
            <div class="pt-3 flex items-center justify-between gap-3 text-xs" style="border-top:1px solid var(--adm-hairline)">
                <div class="truncate text-[11px]" id="status-google" style="color:var(--adm-muted)">
                    {{ $integrations['google']['message'] ?? 'Status terkonfigurasi' }}
                </div>
                <button type="button" id="btn-google" onclick="testGateway('google')" class="adm-btn adm-btn-secondary text-xs py-1 px-2.5 shrink-0">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5" id="icon-google"></i>
                    <span>Uji Koneksi</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 3. System Environment Specification Summary -->
    @if(isset($systemInfo))
    <x-admin.card
        title="Spesifikasi & Lingkungan Runtime Sistem"
        subtitle="Status infrastruktur server, engine database, dan konfigurasi cache/queue"
    >
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="block text-[11px]" style="color:var(--adm-muted)">PHP Version</span>
                <span class="font-semibold adm-mono mt-0.5 block" style="color:var(--adm-ink)">{{ $systemInfo['php_version'] }}</span>
            </div>
            <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="block text-[11px]" style="color:var(--adm-muted)">Laravel Framework</span>
                <span class="font-semibold adm-mono mt-0.5 block" style="color:var(--adm-ink)">v{{ $systemInfo['laravel_version'] }}</span>
            </div>
            <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="block text-[11px]" style="color:var(--adm-muted)">Database Connection</span>
                <span class="font-semibold adm-mono mt-0.5 block uppercase" style="color:var(--adm-ink)">{{ $systemInfo['db_connection'] }}</span>
            </div>
            <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="block text-[11px]" style="color:var(--adm-muted)">App Environment</span>
                <span class="font-semibold adm-mono mt-0.5 block capitalize" style="color:var(--adm-ink)">{{ $systemInfo['app_env'] }}</span>
            </div>
            <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="block text-[11px]" style="color:var(--adm-muted)">Queue Driver</span>
                <span class="font-semibold adm-mono mt-0.5 block capitalize" style="color:var(--adm-ink)">{{ $systemInfo['queue_driver'] }}</span>
            </div>
            <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="block text-[11px]" style="color:var(--adm-muted)">Cache Driver</span>
                <span class="font-semibold adm-mono mt-0.5 block capitalize" style="color:var(--adm-ink)">{{ $systemInfo['cache_driver'] }}</span>
            </div>
            <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="block text-[11px]" style="color:var(--adm-muted)">Public Storage Link</span>
                <span class="font-semibold mt-0.5 flex items-center gap-1.5" style="color:{{ $systemInfo['storage_linked'] ? 'var(--adm-moss)' : 'var(--adm-muted)' }}">
                    <i data-lucide="{{ $systemInfo['storage_linked'] ? 'check' : 'alert-circle' }}" class="w-3.5 h-3.5"></i>
                    <span>{{ $systemInfo['storage_linked'] ? 'Terhubung (Linked)' : 'Belum Dibuat' }}</span>
                </span>
            </div>
            <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="block text-[11px]" style="color:var(--adm-muted)">Server Timestamp</span>
                <span class="font-semibold adm-mono mt-0.5 block" style="color:var(--adm-ink)">{{ $systemInfo['server_time'] }}</span>
            </div>
        </div>
    </x-admin.card>
    @endif

    <!-- 4. Recent Notification Logs Table -->
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

<script>
async function testGateway(gateway) {
    const btn = document.getElementById('btn-' + gateway);
    const icon = document.getElementById('icon-' + gateway);
    const statusEl = document.getElementById('status-' + gateway);
    const badgeEl = document.getElementById('badge-' + gateway);
    if (!btn || !statusEl) return;

    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Memeriksa...</span>`;
    statusEl.innerHTML = `<span style="color:var(--adm-muted)">Sedang melakukan ping diagnostic ke ${gateway}...</span>`;

    try {
        const response = await fetch(`{{ url('/admin/settings/test') }}/${gateway}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        const latencyText = data.latency_ms !== null ? ` [${data.latency_ms}ms]` : '';

        if (data.status === 'connected') {
            statusEl.innerHTML = `<span style="color:var(--adm-moss)" class="font-medium">${data.message}</span>`;
            if (badgeEl) badgeEl.innerHTML = `<span class="adm-badge adm-tone-green"><span>Connected${latencyText}</span></span>`;
        } else if (data.status === 'simulation' || data.status === 'ready') {
            statusEl.innerHTML = `<span style="color:var(--adm-accent)" class="font-medium">${data.message}</span>`;
            if (badgeEl) badgeEl.innerHTML = `<span class="adm-badge adm-tone-blue"><span>Standby${latencyText}</span></span>`;
        } else if (data.status === 'invalid_credentials') {
            statusEl.innerHTML = `<span style="color:var(--adm-rust)" class="font-medium">${data.message}</span>`;
            if (badgeEl) badgeEl.innerHTML = `<span class="adm-badge adm-tone-amber"><span>Auth Failed</span></span>`;
        } else {
            statusEl.innerHTML = `<span style="color:var(--adm-rust)" class="font-medium">${data.message}</span>`;
            if (badgeEl) badgeEl.innerHTML = `<span class="adm-badge adm-tone-rose"><span>Offline${latencyText}</span></span>`;
        }
    } catch (err) {
        statusEl.innerHTML = `<span style="color:var(--adm-rust)">Gagal melakukan pengujian: ${err.message}</span>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalContent;
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }
}

function testAllGateways() {
    ['midtrans', 'fonnte', 'smtp', 'google'].forEach((gw, idx) => {
        setTimeout(() => testGateway(gw), idx * 400);
    });
}
</script>
@endsection
