<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\TradeIn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fase 17.2: Data nyata untuk badge sidebar & chip status integrasi panel Admin/Owner.
        View::composer('layouts.admin', function ($view) {
            if (! Auth::check()) {
                return;
            }

            $midtransKey = (string) config('services.midtrans.server_key', '');
            $fonnteToken = (string) config('services.fonnte.token', '');
            $googleClientId = (string) config('services.google.client_id', '');
            $smtpHost = (string) config('mail.mailers.smtp.host', '');
            $mailDriver = (string) config('mail.default', 'log');

            $midtransOk = $midtransKey !== '' && ! str_contains($midtransKey, 'your_midtrans');
            $fonnteOk = $fonnteToken !== '' && ! str_contains($fonnteToken, 'your_fonnte');
            $googleOk = $googleClientId !== '' && ! str_contains($googleClientId, 'your_google');
            $smtpOk = ! in_array($mailDriver, ['log', 'array']) || ($smtpHost !== '' && ! str_contains($smtpHost, 'localhost'));

            $view->with('shell', [
                'pending_orders' => Order::where('status', 'paid')->count(),
                'pending_trade_ins' => TradeIn::where('status', 'submitted')->count(),
                'midtrans' => [
                    'ok' => $midtransOk,
                    'label' => $midtransOk
                        ? (config('services.midtrans.is_production') ? 'Midtrans Live' : 'Midtrans Sandbox')
                        : 'Midtrans Belum Dikonfigurasi',
                ],
                'fonnte' => [
                    'ok' => $fonnteOk,
                    'label' => $fonnteOk ? 'WhatsApp Aktif' : 'WhatsApp Simulasi',
                ],
                'smtp' => [
                    'ok' => $smtpOk,
                    'label' => $smtpOk ? 'Mailer Host Siap' : 'Mailer Log Simulasi',
                ],
                'google' => [
                    'ok' => $googleOk,
                    'label' => $googleOk ? 'Google SSO Aktif' : 'Google SSO Standby',
                ],
            ]);
        });
    }
}
