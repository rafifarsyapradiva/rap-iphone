<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Services\SystemHealthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    public function __construct(
        protected SystemHealthService $healthService
    ) {}

    /**
     * Halaman Konfigurasi Sistem & Status Integrasi API Pihak Ketiga (/admin/settings).
     */
    public function index(): View
    {
        $status = $this->healthService->getFullStatus(false);
        $integrations = [
            'midtrans' => $status['midtrans'],
            'fonnte' => $status['fonnte'],
            'smtp' => $status['smtp'],
            'google' => $status['google'],
        ];
        $systemInfo = $status['system'];
        $recentLogs = NotificationLog::latest('created_at')->take(10)->get();

        return view('admin.settings', compact('integrations', 'systemInfo', 'recentLogs'));
    }

    /**
     * Endpoint pengujian live koneksi via AJAX (POST /admin/settings/test/{gateway}).
     */
    public function testConnection(string $gateway): JsonResponse
    {
        return match ($gateway) {
            'midtrans' => response()->json($this->healthService->probeMidtrans(true)),
            'fonnte' => response()->json($this->healthService->probeFonnte(true)),
            'smtp' => response()->json($this->healthService->probeSmtp(true)),
            'google' => response()->json($this->healthService->probeGoogleOAuth(true)),
            default => response()->json(['status' => 'error', 'message' => 'Gateway tidak dikenal.'], 404),
        };
    }
}
