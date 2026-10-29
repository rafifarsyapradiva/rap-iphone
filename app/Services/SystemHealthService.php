<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SystemHealthService
{
    /**
     * Mengambil ringkasan status konfigurasi dan koneksi seluruh gateway.
     */
    public function getFullStatus(bool $livePing = false): array
    {
        return [
            'midtrans' => $this->probeMidtrans($livePing),
            'fonnte' => $this->probeFonnte($livePing),
            'smtp' => $this->probeSmtp($livePing),
            'google' => $this->probeGoogleOAuth($livePing),
            'system' => $this->getSystemEnvironmentInfo(),
        ];
    }

    /**
     * Probe konektivitas live ke Midtrans Payment Gateway.
     */
    public function probeMidtrans(bool $livePing = false): array
    {
        $serverKey = (string) config('services.midtrans.server_key', '');
        $isProduction = (bool) config('services.midtrans.is_production', false);
        $baseUrl = $isProduction ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
        $configured = ! empty($serverKey) && ! str_contains($serverKey, 'your_midtrans');

        $result = [
            'key' => 'midtrans',
            'name' => 'Midtrans Payment Gateway (Snap API & Webhook)',
            'configured' => $configured,
            'mode' => $isProduction ? 'Production' : 'Sandbox Mode',
            'webhook_url' => route('api.midtrans.callback'),
            'status' => $configured ? 'configured' : 'unconfigured',
            'latency_ms' => null,
            'message' => $configured
                ? 'Kredensial Server Key terdaftar di konfigurasi .env'
                : 'Server Key belum dikonfigurasi',
        ];

        if ($livePing && $configured) {
            $start = microtime(true);
            try {
                $response = Http::timeout(3)
                    ->withBasicAuth($serverKey, '')
                    ->get("{$baseUrl}/v2/token");

                $result['latency_ms'] = round((microtime(true) - $start) * 1000);
                $statusCode = $response->status();

                if (in_array($statusCode, [200, 400, 404])) {
                    $result['status'] = 'connected';
                    $result['message'] = "Koneksi API Midtrans terhubung aktif ({$result['latency_ms']}ms)";
                } elseif ($statusCode === 401) {
                    $result['status'] = 'invalid_credentials';
                    $result['message'] = 'Server Key Midtrans ditolak (401 Unauthorized)';
                } else {
                    $result['status'] = 'warning';
                    $result['message'] = "Respon Midtrans HTTP {$statusCode}";
                }
            } catch (Exception $e) {
                $result['latency_ms'] = round((microtime(true) - $start) * 1000);
                $result['status'] = 'offline';
                $result['message'] = 'Gagal menghubungi server Midtrans: '.$e->getMessage();
            }
        }

        return $result;
    }

    /**
     * Probe konektivitas live ke Fonnte WhatsApp Gateway.
     */
    public function probeFonnte(bool $livePing = false): array
    {
        $token = (string) config('services.fonnte.token', '');
        $apiUrl = (string) config('services.fonnte.api_url', 'https://api.fonnte.com/send');
        $configured = ! empty($token) && ! str_contains($token, 'your_fonnte');

        $result = [
            'key' => 'fonnte',
            'name' => 'Fonnte WhatsApp Notification Gateway',
            'configured' => $configured,
            'endpoint' => $apiUrl,
            'status' => $configured ? 'configured' : 'simulation',
            'latency_ms' => null,
            'message' => $configured
                ? 'Token Fonnte aktif terpasang'
                : 'Berjalan dalam mode simulasi lokal',
        ];

        if ($livePing && $configured) {
            $start = microtime(true);
            try {
                $response = Http::timeout(3)
                    ->withHeaders(['Authorization' => $token])
                    ->post('https://api.fonnte.com/device');

                $result['latency_ms'] = round((microtime(true) - $start) * 1000);
                if ($response->successful()) {
                    $data = $response->json();
                    $deviceStatus = $data['device_status'] ?? ($data['status'] ? 'connect' : 'unknown');
                    $result['status'] = $deviceStatus === 'connect' ? 'connected' : 'disconnected';
                    $result['message'] = "Fonnte API terhubung. Status device: {$deviceStatus} ({$result['latency_ms']}ms)";
                } else {
                    $result['status'] = 'invalid_credentials';
                    $result['message'] = 'Token Fonnte tidak valid (HTTP '.$response->status().')';
                }
            } catch (Exception $e) {
                $result['latency_ms'] = round((microtime(true) - $start) * 1000);
                $result['status'] = 'offline';
                $result['message'] = 'Gagal menghubungi server Fonnte: '.$e->getMessage();
            }
        }

        return $result;
    }

    /**
     * Probe konektivitas socket SMTP Mailer.
     */
    public function probeSmtp(bool $livePing = false): array
    {
        $driver = config('mail.default', 'log');
        $host = (string) config('mail.mailers.smtp.host', 'localhost');
        $port = (int) config('mail.mailers.smtp.port', 587);
        $encryption = config('mail.mailers.smtp.encryption', 'tls');
        $isLogDriver = in_array($driver, ['log', 'array']);

        $result = [
            'key' => 'smtp',
            'name' => 'Laravel Mailer & DomPDF Invoice Engine',
            'driver' => $driver,
            'host' => $host.($port ? ":{$port}" : '').($encryption ? " ({$encryption})" : ''),
            'status' => $isLogDriver ? 'simulation' : 'configured',
            'latency_ms' => null,
            'message' => $isLogDriver
                ? 'Mail driver menggunakan log lokal (simulasi)'
                : "Host SMTP terdaftar ({$host})",
        ];

        if ($livePing && ! $isLogDriver) {
            $start = microtime(true);
            $errno = 0;
            $errstr = '';
            $socket = @fsockopen($host, $port, $errno, $errstr, 3);

            $result['latency_ms'] = round((microtime(true) - $start) * 1000);
            if ($socket) {
                $banner = fgets($socket, 512);
                fclose($socket);
                $result['status'] = 'connected';
                $result['message'] = 'Socket SMTP terhubung aktif. '.($banner ? 'Banner: '.trim($banner) : '')." ({$result['latency_ms']}ms)";
            } else {
                $result['status'] = 'offline';
                $result['message'] = "Koneksi socket SMTP gagal ke {$host}:{$port} ({$errstr})";
            }
        }

        return $result;
    }

    /**
     * Probe konektivitas Google Console OAuth 2.0 Discovery.
     */
    public function probeGoogleOAuth(bool $livePing = false): array
    {
        $clientId = (string) config('services.google.client_id', '');
        $redirect = (string) config('services.google.redirect', '');
        $configured = ! empty($clientId) && ! str_contains($clientId, 'your_google');

        $result = [
            'key' => 'google',
            'name' => 'Google Console OAuth 2.0 (Socialite)',
            'redirect' => $redirect,
            'configured' => $configured,
            'status' => $configured ? 'connected' : 'ready',
            'latency_ms' => null,
            'message' => $configured
                ? 'Kredensial Google OAuth 2.0 aktif terpasang'
                : 'Siap dikonfigurasi via file .env',
        ];

        if ($livePing) {
            $start = microtime(true);
            try {
                $response = Http::timeout(3)->get('https://accounts.google.com/.well-known/openid-configuration');
                $result['latency_ms'] = round((microtime(true) - $start) * 1000);
                if ($response->successful()) {
                    $result['status'] = $configured ? 'connected' : 'ready';
                    $result['message'] = "Layanan Google Identity Platform aktif & responsif ({$result['latency_ms']}ms)";
                } else {
                    $result['status'] = 'warning';
                    $result['message'] = 'Google Discovery mengembalikan status HTTP '.$response->status();
                }
            } catch (Exception $e) {
                $result['latency_ms'] = round((microtime(true) - $start) * 1000);
                $result['status'] = 'offline';
                $result['message'] = 'Gagal menghubungi server Google: '.$e->getMessage();
            }
        }

        return $result;
    }

    /**
     * Informasi lingkungan runtime sistem.
     */
    public function getSystemEnvironmentInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => (string) config('app.env'),
            'app_debug' => (bool) config('app.debug'),
            'db_connection' => (string) config('database.default'),
            'queue_driver' => (string) config('queue.default'),
            'cache_driver' => (string) config('cache.default'),
            'storage_linked' => file_exists(public_path('storage')),
            'server_time' => now()->format('Y-m-d H:i:s T'),
        ];
    }
}
